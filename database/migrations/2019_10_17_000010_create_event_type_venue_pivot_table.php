<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventTypeVenuePivotTable extends Migration
{
    public function up()
    {
        Schema::create('event_type_venue', function (Blueprint $table) {
            $table->unsignedInteger('venue_id');

            $table->foreign('venue_id', 'venue_id_fk_480176')->references('id')->on('venues')->onDelete('cascade');

            $table->unsignedInteger('event_type_id');

            $table->foreign('event_type_id', 'event_type_id_fk_480176')->references('id')->on('event_types')->onDelete('cascade');
        });
    }
}
