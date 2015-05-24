<?php namespace App\Http\Controllers;

/**
 * Trait to streamline whether a user is logged in with Bow Tie
 *
 * @package App\Http\Controllers
 **/
trait BowTieLogin {
	/**
	 * tests whether the user is logged in via Bow Tie
	 *
	 * @return bool true if logged in, false if not
	 **/
	public function isLoggedInWithBowTie() {
		// verify if the header even exists
		$headers = apache_request_headers();
		if(@isset($headers["X-Bowtie-User-Id"]) === false) {
			return(false);
		}

		// if the bow tie id makes no sense, reject it
		$bowtieUserId = $headers["X-Bowtie-User-Id"];
		$bowtieUserId = filter_var($bowtieUserId, FILTER_VALIDATE_INT);
		if($bowtieUserId === false || $bowtieUserId <= 0) {
			return(false);
		}

		// it must be good at this point
		return(true);
	}

	/**
	 * determines and returns the bow tie user id
	 *
	 * @return int bow tie user id
	 * @throws InvalidArgumentException if the user is not logged in
	 **/
	public function getBowTieUserId() {
		if($this->isLoggedInWithBowTie() === false) {
			throw(new InvalidArgumentException("not logged in with Bow Tie"));
		}
		$headers = apache_request_headers();
		$bowtieUserId = intval($headers["X-Bowtie-User-Id"]);
		return($bowtieUserId);
	}
}