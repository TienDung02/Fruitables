<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class WishlistController extends Controller
{
    public function index()
    {
        // You can pass wishlist data here later
        return Inertia::render('Frontend/Wishlist/Index');
    }
}

