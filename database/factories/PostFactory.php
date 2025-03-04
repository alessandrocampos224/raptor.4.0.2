<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(rand(4, 8));
        return [
            'category_id' => Category::inRandomOrder()->first()->id,
            'name' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(rand(2, 5)),
            'keywords' => implode(', ', fake()->words(rand(3, 6))),
            'image' => 'https://source.unsplash.com/random/800x600/?blog,article,post&' . rand(1, 1000),
            'status' => fake()->randomElement(PostStatus::cases())->value,
        ];
    }

    public function published()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => PostStatus::PUBLISHED->value,
            ];
        });
    }

    public function draft()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => PostStatus::DRAFT->value,
            ];
        });
    }
} 