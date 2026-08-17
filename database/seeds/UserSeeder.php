<?php
use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class)->times(5)->create();

        User::updateOrCreate(
            ['email' => 'demo@instagram.com'],
            [
                'role' => 'user',
                'name' => 'Demo',
                'last_name' => 'Instagram',
                'nick' => 'demo',
                'password' => bcrypt('secret'),
                'image' => \App\Helpers\ImagePlaceholder::make(150, 150, storage_path('app/users')),
            ]
        );
    }
}
