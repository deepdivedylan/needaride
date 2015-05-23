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
			$ride->smallInteger("starts_at")->unsigned();
			$ride->tinyInteger("max_passengers_count")->unsigned();
			$ride->string("description", 1024);
		});
		DB::statement("ALTER TABLE ride ADD COLUMN start POINT NOT NULL AFTER updated_at");
		DB::statement("ALTER TABLE ride ADD COLUMN stop POINT NOT NULL AFTER start");
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
