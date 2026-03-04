<?php

namespace App\Repositories;

use App\Models\Translation;

class TranslationRepository
{
    public function search(array $filters)
    {
        return Translation::with(['tags', 'locale'])
            ->when($filters['locale'] ?? null, fn($q, $code) => $q->whereHas('locale', fn($l) => $l->where('code', $code)))
            ->when($filters['key'] ?? null, fn($q, $v) => $q->where('key', 'like', "%$v%"))
            ->when($filters['content'] ?? null, fn($q, $v) => $q->whereFullText('content', $v))
            ->when($filters['tag'] ?? null, fn($q, $tag) => $q->whereHas('tags', fn($t) => $t->where('name', $tag)))
            ->paginate(50);
    }

    public function find(int $id): Translation
    {
        return Translation::with(['tags','locale'])->findOrFail($id);
    }

    public function export(string $localeCode)
    {
        return Translation::select('key','content')
            ->whereHas('locale', fn($q) => $q->where('code', $localeCode))
            ->cursor();
    }
}