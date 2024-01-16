<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManagePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manage_properties', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_invest_type')->default(false);
            $table->boolean('is_return_type')->default(false);
            $table->boolean('is_installment')->default(false);
            $table->string('fixed_amount')->nullable();
            $table->string('minimum_amount')->nullable();
            $table->string('maximum_amount')->nullable();
            $table->string('total_investment_amount')->nullable();
            $table->string('period_duration')->nullable();
            $table->decimal('profit')->default(0.00);
            $table->integer('profit_type')->nullable();
            $table->decimal('loss')->default(0.00);
            $table->integer('loss_type')->nullable();
            $table->string('total_installments')->nullable();
            $table->string('installment_amount')->nullable();
            $table->integer('installment_duration')->nullable();
            $table->string('installment_duration_type')->nullable();
            $table->string('installment_late_fee')->nullable();
            $table->integer('is_capital_back')->default(0);
            $table->integer('amenity_id')->nullable();
            $table->integer('status')->default(0);
            $table->foreignId('address_id')->index();
            $table->text('location')->nullable();
            $table->text('thumbnail')->nullable()->default(null);
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
        Schema::dropIfExists('manage_properties');
    }
}
