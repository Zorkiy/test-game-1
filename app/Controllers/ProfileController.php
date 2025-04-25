<?php

namespace app\Controllers;

use app\Controllers\MainController;
use app\Models\UserModel;

class ProfileController extends MainController
{
	/**
	 * @return
	 */
	public function indexAction () {
		$userModel = new UserModel;
		$userRow = $userModel->getUserById(1);
		require_once $_SERVER['DOCUMENT_ROOT'] .'/views/profile.php';
	}
}
