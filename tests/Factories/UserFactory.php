<?php

namespace Worksome\DataExport\Tests\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Worksome\DataExport\Tests\Fake\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail(),
        ];
    }
}
