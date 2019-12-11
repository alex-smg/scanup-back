<?php

use Illuminate\Database\Seeder;
use App\{Organization, Person, Question, Response, Survey};

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(Organization::class, 24)->create()->each(function($organization, $key) {
            if ($key % 2 === 0 || $key % 3 === 0 ) {
                $organization->parent = factory(Organization::class)->make();
            }
        });
        factory(Person::class, 10)->create();
        factory(Question::class, 20)->create();
        factory(Response::class, 50)->create();
        factory(Survey::class, 10)->create();
    }
}
