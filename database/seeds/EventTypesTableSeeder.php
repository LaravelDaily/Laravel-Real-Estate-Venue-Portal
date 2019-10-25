<?php

use Illuminate\Database\Seeder;
use App\EventType;
use Illuminate\Support\Str;

class EventTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $eventTypes = [
            'Conferences',
            'Award Ceremonies',
            'The unusual',
            'Private dining',
            'Meeting',
            'Parties'
        ];

        foreach ($eventTypes as $eventType) {
            EventType::create([
                'name' => $eventType,
                'slug' => Str::slug($eventType)
            ]);
        }
    }
}
