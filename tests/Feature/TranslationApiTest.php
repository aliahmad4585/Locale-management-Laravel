<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_create_translation(): void
    {
        $locale = Locale::factory()->create();

        $payload = [
            'locale_id' => $locale->id,
            'key' => 'welcome_message',
            'content' => 'Welcome!',
            'tags' => ['web', 'mobile']
        ];

        $response = $this->actingAs($this->user)->postJson('/api/translations', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['key' => 'welcome_message']);
    }

    public function test_can_list_translations(): void
    {
        $locale = Locale::factory()->create(['code' => 'en']);
        Translation::factory()->count(3)->create(['locale_id' => $locale->id]);

        $response = $this->actingAs($this->user)->getJson('/api/translations');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_cannot_access_api_without_authentication(): void
    {
        $response = $this->getJson('/api/translations');

        $response->assertStatus(401);
    }

    public function test_can_search_translations_by_locale(): void
    {
        $locale = Locale::factory()->create(['code' => 'en']);
        Translation::factory()->count(2)->create(['locale_id' => $locale->id]);

        $response = $this->actingAs($this->user)->getJson('/api/translations?locale=en');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_can_search_translations_by_key(): void
    {
        $locale = Locale::factory()->create();
        Translation::factory()->create(['locale_id' => $locale->id, 'key' => 'app.title']);

        $response = $this->actingAs($this->user)->getJson('/api/translations?key=title');

        $response->assertStatus(200);
    }
}