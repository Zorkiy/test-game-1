<?php

namespace app\controllers;

use app\controllers\MainController;
use app\models\UserModel;

class ProfileController extends MainController
{
	/**
	 * @return void
	 */
	public function indexAction (): void {
		$userModel = new UserModel;
		$userRow = $userModel->getUserById(1);
		require_once $_SERVER['DOCUMENT_ROOT'] .'/views/profile.php';
	}

	/**
	 * @return void
	 */
	public function deleteAction (): void {
		$userModel = new UserModel;
		$userRow = $userModel->getUserById(1);
		die('Видалення користувача'. $userRow['login']);
	}
}
