<?php

namespace Tests\Unit;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Services\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationServiceTest extends TestCase
{
    use RefreshDatabase;

    private TranslationService $service;
    private Locale $locale;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TranslationService();
        $this->locale = Locale::factory()->create();
    }

    public function test_service_creates_translation(): void
    {
        $data = [
            'locale_id' => $this->locale->id,
            'key' => 'hello',
            'content' => 'Hello World',
            'tags' => ['web']
        ];

        $translation = $this->service->create($data);

        $this->assertDatabaseHas('translations', ['key' => 'hello']);
        $this->assertTrue($translation->tags()->where('name', 'web')->exists());
    }

    public function test_service_creates_translation_without_tags(): void
    {
        $data = [
            'locale_id' => $this->locale->id,
            'key' => 'no_tags',
            'content' => 'No Tags Content'
        ];

        $translation = $this->service->create($data);

        $this->assertDatabaseHas('translations', ['key' => 'no_tags']);
        $this->assertCount(0, $translation->tags);
    }

    public function test_service_creates_multiple_tags(): void
    {
        $data = [
            'locale_id' => $this->locale->id,
            'key' => 'multi_tag',
            'content' => 'Multiple Tags',
            'tags' => ['web', 'mobile', 'desktop']
        ];

        $translation = $this->service->create($data);

        $this->assertCount(3, $translation->tags);
        $this->assertTrue($translation->tags()->where('name', 'web')->exists());
        $this->assertTrue($translation->tags()->where('name', 'mobile')->exists());
        $this->assertTrue($translation->tags()->where('name', 'desktop')->exists());
    }

    public function test_service_updates_translation(): void
    {
        $translation = Translation::factory()->create(['locale_id' => $this->locale->id, 'key' => 'old_key']);

        $updated = $this->service->update($translation, [
            'key' => 'new_key',
            'content' => 'New Content',
            'tags' => ['desktop']
        ]);

        $this->assertEquals('new_key', $updated->key);
        $this->assertEquals('New Content', $updated->content);
        $this->assertDatabaseHas('translations', ['key' => 'new_key']);
    }

    public function test_service_updates_translation_tags(): void
    {
        $translation = Translation::factory()->create(['locale_id' => $this->locale->id]);
        $oldTag = Tag::factory()->create(['name' => 'old']);
        $translation->tags()->sync([$oldTag->id]);

        $this->service->update($translation, [
            'content' => 'Updated',
            'tags' => ['new', 'another']
        ]);

        $this->assertFalse($translation->tags()->where('name', 'old')->exists());
        $this->assertTrue($translation->tags()->where('name', 'new')->exists());
        $this->assertTrue($translation->tags()->where('name', 'another')->exists());
    }

    public function test_service_removes_all_tags_on_update(): void
    {
        $translation = Translation::factory()->create(['locale_id' => $this->locale->id]);
        $tag = Tag::factory()->create();
        $translation->tags()->sync([$tag->id]);

        $this->service->update($translation, [
            'content' => 'Updated',
            'tags' => []
        ]);

        $this->assertCount(0, $translation->fresh()->tags);
    }
}