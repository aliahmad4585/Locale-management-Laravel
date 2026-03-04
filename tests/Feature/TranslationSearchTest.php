<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationSearchTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_search_by_locale(): void
    {
        $locale = Locale::factory()->create(['code' => 'en']);
        Translation::factory()->count(3)->create(['locale_id' => $locale->id]);

        $response = $this->actingAs($this->user)->getJson('/api/translations?locale=en');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_search_by_tag(): void
    {
        $locale = Locale::factory()->create();
        $translation = Translation::factory()->create(['locale_id' => $locale->id]);
        $tag = Tag::firstOrCreate(['name' => 'web']);
        $translation->tags()->sync([$tag->id]);

        $response = $this->actingAs($this->user)->getJson('/api/translations?tag=web');

        $response->assertStatus(200)
            ->assertJsonFragment(['key' => $translation->key]);
    }

    public function test_search_by_key(): void
    {
        $locale = Locale::factory()->create();
        $translation = Translation::factory()->create([
            'locale_id' => $locale->id,
            'key' => 'unique_key_test'
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/translations?key=unique_key_test');

        $response->assertStatus(200)
            ->assertJsonFragment(['key' => 'unique_key_test']);
    }

    public function test_search_by_content(): void
    {
        $locale = Locale::factory()->create();
        $translation = Translation::factory()->create([
            'locale_id' => $locale->id,
            'content' => 'Welcome to our application today'
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/translations?content=welcome');

        $response->assertStatus(200);
    }

    public function test_search_requires_authentication(): void
    {
        $response = $this->getJson('/api/translations');

        $response->assertStatus(401);
    }
}