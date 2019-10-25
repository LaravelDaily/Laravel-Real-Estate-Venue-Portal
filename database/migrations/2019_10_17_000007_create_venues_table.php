<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVenuesTable extends Migration
{
    public function up()
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');

            $table->string('slug');

            $table->string('address');

            $table->float('latitude', 15, 8)->nullable();

            $table->float('longitude', 15, 8)->nullable();

            $table->longText('description')->nullable();

            $table->longText('features')->nullable();

            $table->integer('people_minimum')->nullable();

            $table->integer('people_maximum')->nullable();

            $table->decimal('price_per_hour', 15, 2)->nullable();

            $table->boolean('is_featured')->default(0)->nullable();

            $table->timestamps();

            $table->softDeletes();
        });
    }
}
