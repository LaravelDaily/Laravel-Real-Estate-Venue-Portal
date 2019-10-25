<?php

namespace App\Http\Controllers;

use App\Venue;

class VenueController extends Controller
{

    public function show($slug, $id) {
        $venue = Venue::with('event_types', 'location')->where('slug', $slug)->where('id', $id)->firstOrFail();

        $relatedVenues = Venue::with('event_types')->where('location_id', $venue->location_id)->where('id', '!=', $venue->id)->take(3)->get();

        return view('venue', compact('venue', 'relatedVenues'));
    }

}
