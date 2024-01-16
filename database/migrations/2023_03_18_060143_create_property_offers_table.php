<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_share_id')->index()->nullable();
            $table->foreignId('offered_from')->index()->nullable();
            $table->foreignId('offered_to')->index()->nullable();
            $table->foreignId('investment_id')->index()->nullable();
            $table->foreignId('property_id')->index()->nullable();
            $table->decimal('amount')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('property_offers');
    }
}
