<?php

namespace App\Controllers;

use App\Models\Master\UserModel;
use App\Models\Master\SessionModel;
use Ramsey\Uuid\Uuid;

class AuthController extends BaseController
{
    public function index()
    {
        return view('Auth/loginScreen');
    }

    public function processLogin()
    {
        $userModel = new UserModel();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // 1. Validasi input
        if (empty($username) || empty($password)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Username and password are required.'
            ])->setStatusCode(400);
        }

        // 2. Cek user di database
        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user->password)) {
            $user = $userModel->where('username', $username)->first();

            $token = Uuid::uuid7()->toString();

            $sessionModel = new SessionModel();
            $sessionModel->save([
                'id' => $token,
                'user_id'     => $user->id
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Login successful, redirecting...',
                'token' => $token
            ]);
        }

        // 3. Jika gagal
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid username or password.'
        ])->setStatusCode(401);
    }

    public function processLogout()
    {
        $token = $this->request->getPost('token');
        $sessionModel = new SessionModel();
        $sessionModel->delete($token);
        return $this->response->setJSON(null);
    }
}
