<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Ride extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("ride", function(Blueprint $ride) {
			$ride->increments("ride_id")->unsigned();
			$ride->integer("user_id")->unsigned();
			$ride->timestamps();
			$ride->decimal("start_lat", 9, 6);
			$ride->decimal("start_lon", 9, 6);
			$ride->decimal("stop_lat", 9 , 6);
			$ride->decimal("stop_lon", 9, 6);
			$ride->smallInteger("starts_at")->unsigned();
			$ride->tinyInteger("max_passengers_count")->unsigned();
			$ride->string("description", 1024);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop("ride");
	}

}
