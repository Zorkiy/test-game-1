<?php

namespace app\Controllers;

use app\Models\UserModel;

class LoginController
{
    public function indexAction()
    {
        require_once __DIR__ . '/../../views/login.php';
    }

    public function loginAction()
    {
        $login = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $userModel = new UserModel();
        $user = $userModel->getUserByLogin($login);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: http://game.loc/?route=profile');
        } else {
            echo 'Невірний логін або пароль';
        }
    }

    public function logoutAction()
    {
        session_start();
        session_destroy();
        header('Location: http://game.loc/?route=login');
        exit;
    }
}
