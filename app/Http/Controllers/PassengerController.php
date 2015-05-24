<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BowTieLogin;
use App\Passenger;
use App\Ride;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

require_once("BowTieLogin.php");

class PassengerController extends Controller {
	use BowTieLogin;

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
			$ride_id = filter_input(INPUT_POST, "ride_id", FILTER_VALIDATE_INT);

			// verify parameters make sense
			if($ride_id === false || $ride_id <= 0) {
				$reply["status"] = "error";
				$reply["message"] = "Invalid parameters. Verify parameters and try again.";
			} else {
				// verify the ride exists in mySQL
				try {
					// save the ride to mySQL
					$ride = Ride::findOrFail($ride_id);
					$passenger = new Passenger();
					$passenger->user_id = $bowTieUserId;
					$passenger->ride_id = $ride_id;
					$passenger->save();
					$reply["message"] = "Passenger saved successfully.";
				} catch(ModelNotFoundException $modelNotFound) {
					$reply["status"] = "error";
					$reply["message"] = "Ride does not exist. Verify parameters and try again.";
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

			try {
				// delete the passenger from mySQL
				$passenger = Passenger::findOrFail($id);
				if($passenger->user_id === $bowTieUserId) {
					$passenger->delete();
					$reply["message"] = "Passenger deleted successfully.";
				} else {
					$reply["status"] = "error";
					$reply["message"] = "Owner invalid for this ride. Verify parameters and try again.";
				}
			} catch(ModelNotFoundException $modelNotFound) {
				$reply["status"] = "error";
				$reply["message"] = "Ride does not exist. Verify parameters and try again.";
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
