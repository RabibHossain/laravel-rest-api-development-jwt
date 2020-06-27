<?php

namespace App\Http\Controllers;

use App\Channel;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return view('post.create');
    }
}
