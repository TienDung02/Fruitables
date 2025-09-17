<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class WishlistController extends Controller
{
    public function index()
    {
        return Inertia::render('Frontend/Wishlist/Index', [
            'auth' => [
                'user' => auth()->user(),
            ],
        ]);
    }
}
