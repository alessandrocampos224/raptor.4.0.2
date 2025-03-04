<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Database\Factories;

use App\Enums\PageStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'status' => fake()->randomElement(PageStatus::cases())->value,
        ];
    }
} 