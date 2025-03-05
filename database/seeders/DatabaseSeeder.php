<?php

namespace Database\Seeders;

use App\Models\ContentView;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\PostComment;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        if (!User::where('email', 'admin@admin.com')->first()) User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'role' => \App\Role::Admin,
            'password' => bcrypt('123'),
        ]);

        // make 100 users with the role user
        foreach (range(1, 100) as $i) {
            $faker = \Faker\Factory::create();
            User::create([
                'name' => $faker->name(),
                'email' => $faker->freeEmail(),
                'password' => bcrypt('123'),
                'role' => 'user',
                'created_at' => $faker->dateTimeBetween()
            ]);
        }

        foreach (range(1, 10) as $i) {
            $faker = \Faker\Factory::create();
            User::create([
                'name' => $faker->name(),
                'email' => $faker->freeEmail(),
                'password' => bcrypt('123'),
                'role' => 'editor'
            ]);
        }

        foreach(range(1, 10) as $i) {
            $faker = \Faker\Factory::create();
            $name = $faker->sentence();
            PostCategory::create([
                'name' => $name,
                'slug' => str($name)->slug()
            ]);
        }

        foreach(range(1, 200) as $i) {
            $faker = \Faker\Factory::create();
            Post::create([
                'title' => $faker->sentence(),
                'slug' => str($faker->sentence)->slug(),
                'content' => $faker->randomHtml(9),
                'category_id' => PostCategory::inRandomOrder()->first()->id,
                'published_at' => $faker->dateTimeBetween(),
                'created_at' => $faker->dateTimeBetween(),
                'image' => 'https://images.unsplash.com/photo-1740688053492-9d24f32dc53c?q=80&w=2071&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
            ]);
        }

        foreach(range(1, 300) as $i){
            $faker = \Faker\Factory::create();
            PostComment::create([
                'content' => $faker->sentence(),
                'user_id' => User::where('role', 'user')->inRandomOrder()->first()->id,
                'post_id' => Post::inRandomOrder()->first()->id,
                'approved'=> mt_rand(0,1),
                'created_at' => $faker->dateTimeBetween()
            ]);
        }

        foreach(range(1, 2000) as $i) {
            $faker = \Faker\Factory::create();
            ContentView::create([
                'viewable_type' => Post::class,
                'viewable_id' => Post::inRandomOrder()->first()->id,
                'user_id' => User::inRandomOrder()->first()->id,
                'ip' => $faker->ipv4(),
                'watchtime' => mt_rand(0, 500)
            ]);
        }
    }
}
