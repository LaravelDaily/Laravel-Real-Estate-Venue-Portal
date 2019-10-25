<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Location;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locations = [
            'London',
            'Manchester',
            'Liverpool',
            'Birmingham',
        ];

        foreach ($locations as $location) {
            $slug = Str::slug($location);
            $locationObject = Location::create([
                'name' => $location,
                'slug' => $slug
            ]);

            $locationObject->addMediaFromUrl(public_path('images/locations/' . $slug . '.jpg'))->toMediaCollection('photo');
        }
    }
}
