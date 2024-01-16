<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manage_property_id')->index()->nullable();
            $table->string('visitor_ip')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('code')->nullable();
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->string('os_platform')->nullable();
            $table->text('browser')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('analytics');
    }
}
