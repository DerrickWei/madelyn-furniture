<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        if ( !empty( $clientID = $request->session()->get('clientID') ) ) {
            // JUST FOR TESTING!!!!!
            // $clientID='ON053';

            // Load Model Data
            $modelDatas = DB::table('ClientPriceList')
                            ->select('GoodsID')
                            ->where('ClientID', '=', $clientID)
                            ->get()->toArray();

            // Load Client Name
            $clientName = DB::table('client')
                            ->select('Name')
                            ->where('ClientID', '=', $clientID)
                            ->first()->Name;

            $cart = $request->session()->has('cart') ? $request->session()->get('cart') : null;

            return view('welcome', compact('modelDatas', 'cart', 'clientName'));
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
            return redirect('/')->withSuccess('Signed In');
        }

        return redirect('login')->withSuccess('Login details are not valid');
    }

    /**
     * Logout function
     *
     * @param \Illuminate\Http\Request $request
     */
    public function customLogout( Request $request )
    {
        $request->session()->forget('clientID');

        return redirect('login')->withSuccess('Login details are not valid');
    }
}
