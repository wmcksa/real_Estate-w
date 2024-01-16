<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_offer_id')->index()->nullable();
            $table->foreignId('sender_id')->index()->nullable();
            $table->foreignId('receiver_id')->index()->nullable();
            $table->longText('reply')->nullable();
            $table->text('file')->nullable();
            $table->string('status')->default(0);
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
        Schema::dropIfExists('offer_replies');
    }
}
