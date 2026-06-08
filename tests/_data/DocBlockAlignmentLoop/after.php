<?php

namespace App;

class DocBlockLoop {

	public function checkEmails(array $users): void {
		$result = [];

		/** @var \App\User $user */
		foreach ($users as $user) {
			$result[] = $user;
		}
	}

}
