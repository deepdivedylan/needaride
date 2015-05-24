<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BowTieLogin;
use App\Ride;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

		// query mySQL if the BowTie user id exists
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
		// set defaults
		$status = 200;
		$reply = array();

		// query mySQL if the BowTie user id exists
		if($this->isLoggedInWithBowTie() === true) {
			$bowTieUserId = $this->getBowTieUserId();
			$reply["status"] = "OK";

			// sanitize parameters
			var_dump($_POST);
			$start_lat = filter_input(INPUT_POST, "start_lat", FILTER_VALIDATE_FLOAT);
			$start_lon = filter_input(INPUT_POST, "start_lon", FILTER_VALIDATE_FLOAT);
			$stop_lat = filter_input(INPUT_POST, "stop_lat", FILTER_VALIDATE_FLOAT);
			$stop_lon = filter_input(INPUT_POST, "stop_lon", FILTER_VALIDATE_FLOAT);
			$max_passengers_count = filter_input(INPUT_POST, "max_passengers_count", FILTER_VALIDATE_INT);
			$description = filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING);
			$starts_at = filter_input(INPUT_POST, "starts_at", FILTER_VALIDATE_INT);

			// verify parameters make sense
			if($start_lat === false || $start_lon === false || $stop_lat === false || $stop_lon === false ||
				$max_passengers_count === false || empty($description) === true || $starts_at === false ||
				$start_lon < -180.0 || $start_lon > 180.0 || $start_lat < -90.0 || $start_lat > 90.0 ||
				$stop_lon < -180.0 || $stop_lon > 180.0 || $stop_lat < - 90.0 || $start_lat > 90.0 ||
				$max_passengers_count <= 0 || $starts_at < 0 || $starts_at > 86400) {
				$reply["status"] = "error";
				$reply["message"] = "Invalid parameters. Verify parameters and try again.";
			} else {
				// save the ride to mySQL
				$ride = new Ride();
				$ride->user_id = $bowTieUserId;
				$ride->start_lat = $start_lat;
				$ride->start_lon = $start_lon;
				$ride->stop_lat = $stop_lat;
				$ride->stop_lon = $stop_lon;
				$ride->starts_at = $starts_at;
				$ride->max_passengers_count = $max_passengers_count;
				$ride->description = $description;
				$ride->save();
				$reply["message"] = "Ride saved successfully.";
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
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		// set defaults
		$status = 200;
		$reply = array();

		// query mySQL if the BowTie user id exists
		if($this->isLoggedInWithBowTie() === true) {
			$bowTieUserId = $this->getBowTieUserId();
			$reply["status"] = "OK";

			// sanitize input
			$id = filter_var($id, FILTER_VALIDATE_INT);
			if($id === false) {
				$reply["status"] = "error";
				$reply["message"] = "Invalid parameters. Verify parameters and try again.";
			} else {
				try {
					// grab the ride from mySQL
					$ride = Ride::findorFail($id);
					$reply = $ride;
					$reply->status = "OK";
				} catch(ModelNotFoundException $modelNotFound) {
					// report the ride does not exist
					$reply["status"] = "error";
					$reply["message"] = "Ride not found. Verify parameters and try again.";
				}
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
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		// set defaults
		$status = 200;
		$reply = array();

		// query mySQL if the BowTie user id exists
		if($this->isLoggedInWithBowTie() === true) {
			$bowTieUserId = $this->getBowTieUserId();
			$reply["status"] = "OK";

			// sanitize input
			$id = filter_var($id, FILTER_VALIDATE_INT);
			if($id === false) {
				$reply["status"] = "error";
				$reply["message"] = "Invalid parameters. Verify parameters and try again.";
			} else {
				try {
					// delete the ride from mySQL
					$ride = Ride::findorFail($id);
					$reply["status"] = "OK";
					if($ride->user_id === $bowTieUserId) {
						$ride->delete();
						$reply["message"] = "Ride successfully deleted.";
					} else {
						$reply["message"] = "Ride can only be deleted by the ride owner.";
					}
				} catch(ModelNotFoundException $modelNotFound) {
					// report the ride does not exist
					$reply["status"] = "error";
					$reply["message"] = "Ride not found. Verify parameters and try again.";
				}
			}
		} else {
			// generate an error if not logged in
			$status = 401;
			$reply["status"] = "error";
			$reply["message"] = "You are not logged into Bow Tie. Please login and try again.";
		}

		return(response()->json($reply, $status));
	}
}
