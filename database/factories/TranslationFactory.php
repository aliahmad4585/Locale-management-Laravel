<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Translation;
use App\Models\Locale;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation>
 */
class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $localeId = Locale::inRandomOrder()->first()?->id ?? 1;

        return [
            'locale_id' => $localeId,
            'key' => $this->faker->unique()->uuid,
            'content' => $this->faker->sentence,
        ];
    }
}
