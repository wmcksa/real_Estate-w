<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRankingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ranking_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ranking_id')->index();
            $table->integer('language_id');
            $table->string('rank_name')->nullable();
            $table->string('rank_level')->nullable();
            $table->string('rank_lavel_unq')->nullable();
            $table->longText('description')->nullable();
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
        Schema::dropIfExists('ranking_details');
    }
}
