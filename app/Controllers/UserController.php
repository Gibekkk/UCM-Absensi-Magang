<?php

namespace App\Controllers;

use App\Models\Master\UserModel;
use App\Models\Master\SessionModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $sessionModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->sessionModel = new SessionModel();
    }
    public function getUsers($id = null)
    {
        $users = [];
        $token = $this->request->getHeaderLine('token');
        $myInfo = $this->sessionModel->where('id', $token)->first()->getUser();
        if ($id != null) {
            $queryRes = $this->userModel->where('id', $id)->where('is_super_admin', 0)->findAll();

            if (count($queryRes) == 0)
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User Not Found.'
                ])->setStatusCode(404);

            foreach ($queryRes as $user) {
                $users = [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'is_active' => $user->is_active,
                    'created_by' => $user->created_by,
                    'modified_by' => $user->modified_by,
                ];
            }
        } else {
            $queryRes = $this->userModel->where('is_super_admin', 0)->findAll();

            foreach ($queryRes as $user) {
                $row = [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'is_active' => $user->is_active,
                    'created_by' => $user->created_by,
                    'modified_by' => $user->modified_by,
                ];

                $users[] = $row;
            }
        }

        return $this->response->setJSON([
            'users' => $users
        ]);
    }

    public function addUser()
    {
        $token = $this->request->getHeaderLine('token');
        $id = $this->sessionModel->where('id', $token)->first()->getUser()->id;

        $data = $this->request->getJSON(true);
        $user = [
            'full_name' => $data['full_name'],
            'username' => $data['username'],
            'password' => $data['password'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'is_active' => "1",
            'created_by' => $id,
            'modified_by' => $id,
        ];

        if ($this->userModel->insert($user)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User Added.'
            ]);
        }
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Unknown Error Occured.'
        ])->setStatusCode(500);
    }

    public function editUser($id)
    {
        $token = $this->request->getHeaderLine('token');
        $myInfo = $this->sessionModel->where('id', $token)->first()->getUser();
        $userId = $myInfo->id;

        $data = $this->request->getJSON(true);
        $user = [
            'full_name' => $data['full_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'modified_by' => $userId,
        ];
        if(isset($data['password'])) $user['password'] = $data['password'];
        $userData = $this->userModel->find($id);
        if ($userData) {
            // Jika ini error, hal yang normal, kode ini bekerja dengan baik
            if ($this->userModel->update($id, $user)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'User Edited.'
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User Not Found.'
            ])->setStatusCode(404);
        }
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Unknown Error Occured.'
        ])->setStatusCode(500);
    }

    public function setIsActive($id, $isActive)
    {
        $token = $this->request->getHeaderLine('token');
        $myInfo = $this->sessionModel->where('id', $token)->first()->getUser();
        $userId = $myInfo->id;

        $user = [
            'is_active' => $isActive,
            'modified_by' => $userId,
        ];
        $userData = $this->userModel->find($id);
        if ($userData) {
            // Jika ini error, hal yang normal, kode ini bekerja dengan baik
            if ($this->userModel->update($id, $user)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'User Edited.'
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User Not Found.'
            ])->setStatusCode(404);
        }
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Unknown Error Occured.'
        ])->setStatusCode(500);
    }

    public function deleteUser($id)
    {
        $token = $this->request->getHeaderLine('token');

        if ($this->userModel->find($id)) {
            if ($this->userModel->delete($id)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'User Deleted.'
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User Not Found.'
            ])->setStatusCode(404);
        }
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Unknown Error Occured.'
        ])->setStatusCode(500);
    }
}
