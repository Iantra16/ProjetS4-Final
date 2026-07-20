<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if ($this->request->is('post')) { 
            $model = new UserModel();
            $user = $model->where('email', $this->request->getPost('email'))->first();

            if ($user && password_verify($this->request->getPost('password'), $user['password'])) {
                session()->set([
                    'user_id' => $user['id'],
                    'nom'     => $user['nom'],
                    'role'    => $user['role'],
                ]);
                return redirect()->to($user['role'] === 'admin' ? '/admin/dashboard' : '/');
            }

            return redirect()->back()->with('error', 'Email ou mot de passe incorrect');
        }

        return view('auth/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
