<?php
namespace App\Controller;

use App\Model\Users;
use Craft\Application\Hash as Hash;
use Craft\Application\Session;
use Exception;

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
        return $this->render('users.list');
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

        $hashed = Hash::bcrypt($password, ['cost' => 12]);
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

        if (empty($input)) {
            return ['code' => 422, 'error' => 'No input data provided'];
        }

        $existingUser = $this->users->where('id', '=', (int) $id)->first();
        if (!$existingUser) {
            return ['code' => 404, 'error' => 'User not found'];
        }

        $data = [];
        if (isset($input['name']) && !empty(trim($input['name'])))
            $data['name'] = trim($input['name']);
        if (isset($input['email']) && !empty(trim($input['email'])))
            $data['email'] = trim($input['email']);
        if (isset($input['password']) && !empty(trim($input['password'])))
            $data['password'] = Hash::bcrypt(trim($input['password']), ['cost' => 12]);

        if (empty($data)) {
            return ['code' => 422, 'error' => 'No valid fields to update'];
        }

        try {
            error_log("Updating user ID: $id with data: " . json_encode($data));
            
            $ok = $this->users->where('id', '=', (int) $id)->executeUpdate($data);
            
            error_log("Update result: " . ($ok ? 'success' : 'failed'));
            
            if ($ok) {
                return ['code' => 200, 'updated' => true, 'message' => 'User updated successfully'];
            } else {
                return ['code' => 500, 'error' => 'Update operation failed - no rows affected'];
            }
        } catch (Exception $e) {
            error_log("Update error: " . $e->getMessage());
            return ['code' => 500, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function usersDestroy($id)
    {
        $existingUser = $this->users->where('id', '=', (int) $id)->first();
        if (!$existingUser) {
            return ['code' => 404, 'error' => 'User not found'];
        }

        try {
            $ok = $this->users->where('id', '=', (int) $id)->executeDelete();
            
            if ($ok) {
                return ['code' => 200, 'deleted' => true, 'message' => 'User deleted successfully'];
            } else {
                return ['code' => 500, 'error' => 'Delete operation failed'];
            }
        } catch (Exception $e) {
            return ['code' => 500, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
}