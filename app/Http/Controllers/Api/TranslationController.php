<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TranslationRequest;
use App\Http\Resources\TranslationResource;
use App\Models\Translation;
use App\Repositories\TranslationRepository;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TranslationController extends Controller
{
    public function __construct(
        private TranslationRepository $repo,
        private TranslationService $service
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['locale', 'key', 'content', 'tag']);
        $translations = $this->repo->search($filters);
        return TranslationResource::collection($translations);
    }

    public function show(int $id)
    {
        $translation = $this->repo->find($id);
        return new TranslationResource($translation);
    }

    public function store(TranslationRequest $request)
    {
        $translation = $this->service->create($request->validated());
        return new TranslationResource($translation);
    }

    public function update(TranslationRequest $request, Translation $translation)
    {
        $updated = $this->service->update($translation, $request->validated());
        return new TranslationResource($updated);
    }

    public function destroy(Translation $translation)
    {
        $translation->delete();
        Cache::tags(['translations'])->flush();
        return response()->json(['message' => 'Translation deleted'], 200);
    }

    public function export(string $locale)
    {
        $translations = $this->repo->export($locale);
        
        $data = [];
        foreach ($translations as $t) {
            $data[$t->key] = $t->content;
        }

        return response()->json($data);
    }
}
