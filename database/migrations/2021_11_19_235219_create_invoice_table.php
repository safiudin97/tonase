<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('invoice_no')->unique();
            $table->string('amount');
            $table->text('description');
            $table->date('date_of_invoice');
            $table->time('time_of_invoice');
            $table->enum('is_payment', ['n', 'y'])->default('n');
            $table->integer('billing_id')->nullable();
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
        Schema::dropIfExists('invoice');
    }
}
