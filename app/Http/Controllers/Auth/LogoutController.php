<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    protected function loggedOut(Request $request)
    {
        return redirect('/login-admin'); // Akan mengarahkan ke halaman awal
    }
}