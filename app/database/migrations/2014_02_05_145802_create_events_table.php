<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('events', function(Blueprint $table) {
            $table->engine = 'InnoDB';
			$table->increments('id');
			$table->integer('category_id');
			$table->integer('user_id');
			$table->integer('location_id');
            $table->string('title');
            $table->string('title_en');
            $table->text('description');
            $table->text('description_en');
            $table->boolean('free');
            $table->decimal('price',5,2);
            $table->integer('total_seats');
            $table->integer('available_seats');
            $table->string('slug');
            $table->timestamp('date_start'); // here also
            $table->timestamp('date_end'); // just for now // later you make it date !!
            $table->text('address');
            $table->text('address_en');
            $table->string('street');
            $table->string('street_en');
            $table->float('latitude',10,6);
            $table->float('longitude',10,6);
            $table->boolean('active');
            $table->string('button');
            $table->string('button_en');
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
		Schema::drop('events');
	}

}
