<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_translations_csv(): void
    {
        $user = User::factory()->create();
        $locale = Locale::factory()->create(['code' => 'en']);
        $translations = Translation::factory()->count(5)->create(['locale_id' => $locale->id]);

        $response = $this->actingAs($user)->getJson('/api/export/en');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');

        foreach ($translations as $translation) {
            $response->assertSee($translation->key);
            $response->assertSee($translation->content);
        }
    }

    public function test_export_requires_authentication(): void
    {
        $locale = Locale::factory()->create(['code' => 'en']);
        Translation::factory()->count(3)->create(['locale_id' => $locale->id]);

        $response = $this->getJson('/api/export/en');

        $response->assertStatus(401);
    }

    public function test_export_nonexistent_locale(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/export/xyz');

        $response->assertStatus(200);
    }
}