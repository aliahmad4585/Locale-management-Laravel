<?php

namespace App\Services;

use App\Models\Translation;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TranslationService
{
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $translation = Translation::create($data);
            $this->syncTags($translation, $data['tags'] ?? []);
            Cache::tags(['translations'])->flush();
            return $translation;
        });
    }

    public function update(Translation $translation, array $data)
    {
        return DB::transaction(function () use ($translation, $data) {
            $translation->update($data);
            $this->syncTags($translation, $data['tags'] ?? []);
            Cache::tags(['translations'])->flush();
            return $translation;
        });
    }

    private function syncTags(Translation $translation, array $tags)
    {
        $tagIds = collect($tags)->map(fn($name) => Tag::firstOrCreate(['name'=>$name])->id);
        $translation->tags()->sync($tagIds);
    }
}