<?php
namespace App\Controller;

use Craft\Application\Session;

class HomeController extends Controller
{
    public function index()
    {
    $random = rand(1000, 9999);
    Session::flash('message', 'Chào mừng bạn đến với trang chủ! Mã: ' . $random);
    $message = $_SESSION['_flash']['message'] ?? null;
    return $this->render('home', ['message' => $message]);
    }
}