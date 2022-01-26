<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class CustomAuthController extends Controller
{
    /**
     * Index view function
     *
     * @param \Illuminate\Http\Request $request
     * @return view
     */
    public function index( Request $request )
    {
        // Check if user Logged in
        if ( !empty( $request->session()->get('clientID') ) ) {
            return view('welcome');
        } else {
            return view('login');
        }
    }

    /**
     * Login view function
     *
     * @param \Illuminate\Http\Request $request
     * @return view
     */
    public function login( Request $request )
    {
        return view('login');
    }

    /**
     * Login function
     *
     * @param \Illuminate\Http\Request $request
     */
    public function customLogin( Request $request )
    {
        $request->validate([
            'clientID'  => 'required',
            'password'  => 'required'
        ]);

        // Check User Credential
        if ( Client::where([
            [ 'ClientID', '=', $request->clientID ],
            [ 'Password', '=', $request->password ]
        ]) ) {
            $request->session()->put('clientID', $request->clientID);
            return redirect()->intend('index')->withSuccess('Signed In');
        }

        return redirect("login")->withSuccess('Login details are not valid');
    }
}
