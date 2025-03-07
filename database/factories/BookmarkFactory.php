<?php

namespace Database\Factories;

use App\Models\Bookmark;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bookmark>
 */
class BookmarkFactory extends Factory
{
     /**
     * The name of the model that is associated with this factory.
     *
     * @var string
     */
    protected $model = Bookmark::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => $this->faker->url(),
            'title' => $this->faker->sentence(5), // Generate a random title (5 words)
            'description' => $this->faker->text(100), // Generate a random description (100 characters)
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
