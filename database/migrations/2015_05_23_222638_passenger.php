<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Passenger extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("passenger", function(Blueprint $passenger) {
			$passenger->increments("passenger_id");
			$passenger->integer("ride_id")->unsigned()->index();
			$passenger->timestamps();
			$passenger->foreign("ride_id")->references("ride_id")->on("ride");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop("passenger");
	}

}
