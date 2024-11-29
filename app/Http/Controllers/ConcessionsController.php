<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConcessionsController extends Controller
{
    public function view()
    {
        return view('concession.view');
    }
}