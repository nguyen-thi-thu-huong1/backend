<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEeziepayHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eeziepay_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id');
            $table->string('billno')->nullable();
            $table->string('partner_orderid');
            $table->string('bank_code')->nullable();
            $table->string('currency');
            $table->integer('request_amount');
            $table->integer('receive_amount')->nullable();
            $table->integer('fee')->nullable();
            $table->string('status', 4);
            $table->dateTime('transaction_at')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('eeziepay_histories');
    }
}
