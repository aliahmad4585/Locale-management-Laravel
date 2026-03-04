<?php

namespace Database\Seeders;

use App\Models\Translation;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = ['web', 'mobile', 'desktop'];

        Translation::factory(5000)->create()->each(function ($translation) use ($tags) {
            $tagIds = [];
            foreach ($tags as $tag) {
                $tagIds[] = Tag::firstOrCreate(['name' => $tag])->id;
            }
            $translation->tags()->sync($tagIds);
        });
    }
}
