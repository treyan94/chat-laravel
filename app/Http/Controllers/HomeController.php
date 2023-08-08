<?php

namespace App\Http\Controllers;

use Auth;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function users(): Response
    {
        return Inertia::render('Users');
    }

    public function home(): Response
    {
        return Inertia::render('Home', ['user' => Auth::user()]);
    }
}
