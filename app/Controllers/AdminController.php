<?php

namespace App\Controllers;

class AdminController extends BaseController
{
    public function index()
    {
        return view('Admin/students');
    }
    public function internship()
    {
        return view('Admin/internship');
    }
    public function attendance()
    {
        return view('Admin/attendance');
    }
}
