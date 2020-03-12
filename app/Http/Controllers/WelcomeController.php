<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Show the application splash screen.
     *
     * @return Response
     */
    public function show(Request $request)
    {
        $rv = array(
            "page" => array(
                "feedLoad" => 'home',
                "title" => 'Link Slick'
            ),
        );
        return view('welcome')->with($rv);
    }
}
