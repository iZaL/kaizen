<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payments', function(Blueprint $table)
		{
            $table->increments('id');
            $table->integer('user_id');
            $table->string('method')->nullable(); // paypal, creditcard, knet
            $table->morphs('payable'); // EventModel, Package
            $table->decimal('amount',6,2)->nullable();
            $table->string('status')->nullable(); // [APPROVED, REJECTED]
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
        Schema::drop('payments');
    }


}
