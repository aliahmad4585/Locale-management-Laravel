<?php

namespace Tests\Unit;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Repositories\TranslationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TranslationRepository $repo;
    private Locale $locale;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new TranslationRepository();
        $this->locale = Locale::factory()->create(['code' => 'en']);
    }

    public function test_find_translation_by_id(): void
    {
        $translation = Translation::factory()->create(['locale_id' => $this->locale->id]);
        $found = $this->repo->find($translation->id);

        $this->assertEquals($translation->id, $found->id);
    }

    public function test_search_translation_by_key(): void
    {
        $translation = Translation::factory()->create([
            'locale_id' => $this->locale->id,
            'key' => 'search_key_test'
        ]);

        $results = $this->repo->search(['key' => 'search_key_test']);
        $this->assertTrue($results->contains('key', 'search_key_test'));
    }

    public function test_search_by_locale(): void
    {
        Translation::factory()->count(2)->create(['locale_id' => $this->locale->id]);
        
        $results = $this->repo->search(['locale' => 'en']);
        $this->assertGreaterThanOrEqual(2, $results->count());
    }

    public function test_search_by_tag(): void
    {
        $tag = Tag::factory()->create(['name' => 'important']);
        $translation = Translation::factory()->create(['locale_id' => $this->locale->id]);
        $translation->tags()->attach($tag->id);

        $results = $this->repo->search(['tag' => 'important']);
        $this->assertTrue($results->contains('id', $translation->id));
    }

    public function test_export_returns_cursor(): void
    {
        Translation::factory()->count(3)->create(['locale_id' => $this->locale->id]);

        $cursor = $this->repo->export('en');
        $this->assertIsIterable($cursor);
        $this->assertCount(3, $cursor->toArray());
    }
}