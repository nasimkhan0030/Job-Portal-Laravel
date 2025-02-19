<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //This method will return the view of the home page
    public function index()
    {
        return view('Front.home');
    }
}
