<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = \App\Models\Tenant::all();

        foreach ($tenants as $tenant) {
            $user =  User::factory()->create([
                'name' => 'Admin',
                'email' => sprintf('admin@%s', $tenant->domain),
                'password' => bcrypt('12345678'),
            ]);

            $tenant->users()->attach($user);
            $user->roles()->attach($tenant->roles()->where('slug', 'admin')->first());

            $user =  User::factory()->create([
                'name' => 'User',
                'email' => sprintf('user@%s', $tenant->domain),
                'password' => bcrypt('12345678'),
            ]);
            $tenant->users()->attach($user);
            $user->roles()->attach($tenant->roles()->where('slug', 'user')->first());


            $tenant->users()->attach(User::factory(10)->create());
        }
    }
}
