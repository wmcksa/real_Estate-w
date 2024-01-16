<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferLocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_locks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_offer_id')->index()->nullable();
            $table->foreignId('property_share_id')->index()->nullable();
            $table->decimal('offer_amount')->nullable();
            $table->decimal('lock_amount')->nullable();
            $table->dateTime('duration')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('offer_locks');
    }
}
