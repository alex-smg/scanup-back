<?php

use Illuminate\Database\Seeder;
use App\{Organization, Person, Question, Response, Survey};
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'moderator']);
        Role::create(['name' => 'viewer']);
//
//        factory(Organization::class, 22)->create()->each(function($organization, $key) {
//            if ($key % 2 === 0 || $key % 3 === 0 ) {
//                $organization->parent()->associate(factory(Organization::class)->create());
//            }
//        });
//        factory(Person::class, 10)->create()->each(function($person) {
//            $person->assignRole('super-admin');
//        });
//        factory(Question::class, 20)->create();
//        factory(Response::class, 50)->create();
//        factory(Survey::class, 10)->create();

    }
}
