<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOneBillingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('one_billing', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('billing_no');
            $table->string('total_amount')->nullable();
            $table->enum('is_payment', ['n', 'y'])->default('n');
            $table->date('payment_date')->nullable();
            $table->time('payment_time')->nullable();
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
        Schema::dropIfExists('one_billing');
    }
}
