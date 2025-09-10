<?php
namespace App\Controller;

use App\Model\Users;
use Craft\Application\Session;

class HomeController extends Controller
{
    public $users;
    public function __construct()
    {
        $this->users = new Users();
    }

    public function getAdapter()
    {
        return $this->users->getAdapter();
    }

    public function index()
    {
        $random = rand(1000, 9999);
        Session::flash('message', 'Chào mừng bạn đến với trang chủ! Mã: ' . $random);
        $message = Session::getFlash('message');
        return $this->render('home', ['message' => $message]);
    }

    public function test()
    {
        $allUsers = $this->users->select()->fetchAll();
        return $this->render('users.list', ['users' => $allUsers]);
    }

    // --- API: Users CRUD using Query Builder ---
    public function usersIndex()
    {
        $users = $this->users->select()->fetchAll();
        return ['data' => $users];
    }

    public function usersStore()
    {
        $input = $_POST;
        if (empty($input)) {
            $raw = file_get_contents('php://input');
            $json = json_decode($raw, true);
            if (is_array($json)) {
                $input = $json;
            }
        }

        $name = $input['name'] ?? null;
        $email = $input['email'] ?? null;
        $password = $input['password'] ?? null;
        if (!$name || !$email || !$password) {
            return ['code' => 422, 'error' => 'name, email, password are required'];
        }

        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $id = $this->users->insertGetId([
            'name' => $name,
            'email' => $email,
            'password' => $hashed,
        ]);

        return ['code' => 201, 'id' => $id];
    }

    public function usersUpdate($id)
    {
        $input = $_POST;
        if (empty($input)) {
            $raw = file_get_contents('php://input');
            $json = json_decode($raw, true);
            if (is_array($json)) {
                $input = $json;
            }
        }

        $data = [];
        if (isset($input['name'])) $data['name'] = $input['name'];
        if (isset($input['email'])) $data['email'] = $input['email'];
        if (isset($input['password'])) $data['password'] = password_hash($input['password'], PASSWORD_BCRYPT);

        if (empty($data)) {
            return ['code' => 422, 'error' => 'No fields to update'];
        }

        $ok = $this->users->where('id', '=', (int)$id)->executeUpdate($data);
        return ['updated' => (bool)$ok];
    }

    public function usersDestroy($id)
    {
        $ok = $this->users->where('id', '=', (int)$id)->executeDelete();
        return ['deleted' => (bool)$ok];
    }
}