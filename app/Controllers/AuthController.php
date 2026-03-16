<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SessionModel;

class AuthController extends BaseController
{
    public function index()
    {
        // Jika sudah ada cookie token, cek validitasnya
        $token = $_COOKIE['token'] ?? null;
        if ($token) {
            $sessionModel = new SessionModel();
            if ($sessionModel->find($token)) {
                return redirect()->to('/admin/home');
            }
        }
        return view('login');
    }

    public function login()
    {
        $userModel = new UserModel();
        $sessionModel = new SessionModel();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Cari user berdasarkan username
        $user = $userModel->where('username', $username)->first();

        // Verifikasi password manual hashing
        if ($user && password_verify($password, $user->password)) {
            
            // Hapus session lama user ini (opsional)
            $sessionModel->where('user_id', $user->id)->delete();

            // Buat token baru (UUID otomatis digenerate oleh Model)
            $sessionModel->insert(['user_id' => $user->id]);
            
            // Ambil ID token yang baru dibuat
            $token = $sessionModel->where('user_id', $user->id)->first()->id;

            // Simpan ke Cookie (berlaku 2 jam)
            setcookie("token", $token, time() + 7200, "/", "", false, true);

            return redirect()->to('/admin/home');
        }

        // Jika gagal, kembalikan dengan pesan error
        return redirect()->back()->with('error', 'Username atau Password salah!');
    }

    public function logout()
    {
        $token = $_COOKIE['token'] ?? null;
        if ($token) {
            $sessionModel = new SessionModel();
            $sessionModel->delete($token);
        }

        // Hapus cookie
        setcookie("token", "", time() - 3600, "/");
        
        return redirect()->to('/')->with('success', 'Berhasil logout');
    }
}