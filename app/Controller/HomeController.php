<?php
namespace App\Controller;

use App\Model\Users;
use Craft\Application\Hash as Hash;
use Craft\Application\Session;

class HomeController extends Controller
{
    public $users;
    public function __construct()
    {
        $this->users = new Users();
    }

    public function index()
    {
        dump(session(), getBaseUrl());
        $random = rand(1000, 9999);
        flash('message', 'Chào mừng bạn đến với trang chủ! Mã: ' . $random);
        $message = getFlash('message');
        $testHash = Hash::default("password123");
        $testHash2 = Hash::bcrypt("password123", ['cost' => 12]);
        $testHash3 = Hash::argon2i("password123", [
            'memory_cost' => 1<<17,
            'time_cost'   => 4,
            'threads'     => 2,
        ]);
        $testVerify = Hash::verify("password123", $testHash);
        return $this->render(
            'home',
            [
                'message' => $message,
                'testHash' => $testHash,
                'testHash2' => $testHash2,
                'testHash3' => $testHash3,
                'testVerify' => $testVerify
            ]
        );
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
        if (isset($input['name']))
            $data['name'] = $input['name'];
        if (isset($input['email']))
            $data['email'] = $input['email'];
        if (isset($input['password']))
            $data['password'] = password_hash($input['password'], PASSWORD_BCRYPT);

        if (empty($data)) {
            return ['code' => 422, 'error' => 'No fields to update'];
        }

        $ok = $this->users->where('id', '=', (int) $id)->executeUpdate($data);
        return ['updated' => (bool) $ok];
    }

    public function usersDestroy($id)
    {
        $ok = $this->users->where('id', '=', (int) $id)->executeDelete();
        return ['deleted' => (bool) $ok];
    }
}