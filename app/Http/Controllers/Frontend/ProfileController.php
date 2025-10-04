<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the profile page
     */
    public function index(): Response
    {
        return Inertia::render('Frontend/Profile/Index');
    }
}
