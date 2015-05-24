<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BowTieLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

require_once("BowTieLogin.php");

class RideController extends Controller {
	use BowTieLogin;

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		// set defaults
		$status = 200;
		$reply = array();

		// query mySQL if the BowTie user id existys
		if($this->isLoggedInWithBowTie() === true) {
			$bowTieUserId = $this->getBowTieUserId();
			$reply["status"] = "OK";
			$reply["rides"] = DB::table("ride")->where("user_id", "=", $bowTieUserId)->get();

			// reformat the GPS coordinates
			$deleteKeys = array("start_lat", "start_lon", "stop_lat", "stop_lon");
			foreach($reply["rides"] as $ride) {
				$start = array("latitude" => $ride->start_lat, "longitude" => $ride->start_lon);
				$stop = array("latitude" => $ride->stop_lat, "longitude" => $ride->stop_lon);
				$ride->start = $start;
				$ride->stop = $stop;

				// delete the raw mySQL data from this representation
				foreach($deleteKeys as $key) {
					unset($ride->$key);
				}

				// grab the passengers for this ride
				$passengers = DB::table("passenger")->where("ride_id", "=", $ride->ride_id)->lists("user_id");
				$ride->passengers_count = count($passengers);
				$ride->passengers = $passengers;

				// delete created/update fields from public view
				unset($ride->created_at);
				unset($ride->updated_at);
			}
		} else {
			// generate an error if not logged in
			$status = 401;
			$reply["status"] = "error";
			$reply["message"] = "You are not logged into Bow Tie. Please login and try again.";
		}

		return(response()->json($reply, $status));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
