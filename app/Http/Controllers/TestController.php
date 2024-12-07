<?php

namespace App\Http\Controllers;

use App\Mail\RegisterVerifyMail;
use App\Models\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $mail = new RegisterVerifyMail(User::find(7), 'token');

        return $mail->render();
    }
}
