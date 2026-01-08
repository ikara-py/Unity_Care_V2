<?php
session_start();
require_once __DIR__ . '/../classes/repositories/UserRepository.php';

class Auth
{
    private static $user = null;
    public static function check()
    {
        return isset($_SESSION['user_id']);
    }

    public static function user()
    {
        if (!self::check()) return null;
        if (self::$user === null) {
            $repo = new UserRepository();
            self::$user = $repo->find($_SESSION['user_id']);
        }
        return self::$user;
    }

    public static function login($userId)
    {
        $_SESSION['user_id'] = $userId;
        session_regenerate_id(true);
    }

    public static function logout()
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function requireLogin()
    {
        if (!self::check()) {
            header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit();
        }
    }

    public static function requireAdmin()
    {
        self::requireLogin();
        $user = self::user();
        if ($user['role'] !== 'admin') {
            header('Location: index.php');
            exit();
        }
    }
    public static function requireDoctor()
    {
        self::requireLogin();
        $user = self::user();
        if ($user['role'] !== 'doctor' && $user['role'] !== 'admin') {
            header('Location: index.php');
            exit();
        }
    }

    public static function requirePatient()
    {
        self::requireLogin();
        $user = self::user();
        if ($user['role'] !== 'patient' && $user['role'] !== 'admin') {
            header('Location: index.php');
            exit();
        }
    }
}
