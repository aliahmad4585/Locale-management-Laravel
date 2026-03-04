<?php

namespace App\Console\Commands;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Console\Command;

class SeedTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-translations {count=1000 : Number of translations to seed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed large number of translations for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->argument('count');
        $tags = ['web', 'mobile', 'desktop'];

        $this->info("Seeding $count translations...");

        Translation::factory($count)->create()->each(function ($translation) use ($tags) {
            $tagIds = [];
            foreach ($tags as $tag) {
                $tagIds[] = Tag::firstOrCreate(['name' => $tag])->id;
            }
            $translation->tags()->sync($tagIds);
        });

        $this->info("Seeding completed!");
    }
}
