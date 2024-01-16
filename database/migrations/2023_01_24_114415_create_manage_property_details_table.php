<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManagePropertyDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manage_property_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manage_property_id')->index();
            $table->integer('language_id');
            $table->text('property_title')->nullable();
            $table->longText('details')->nullable();
            $table->longText('faq')->nullable();

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
        Schema::dropIfExists('manage_property_details');
    }
}
