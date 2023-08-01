<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Article',
            'subtitle' => 'Explore now...',
            'posts' => Story::latest()->paginate(7)->withQueryString()
        ];

        return view('index', $data);
    }
}
