<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomDataController extends Controller
{
    /**
     * Get Panel from user selection
     *
     * @param \Illuminate\Http\Request $request
     * @return json
     */
    public function getPanel ( Request $request )
    {
        $model = ( $request->all() )['model'];
        $request->session()->put('model', $model);

        // Get clientID
        $clientID = $request->session()->get('clientID');

        // JUST FOR TESTING!!!!!
        // $clientID='ON053';

        // Load Panel Data
        $PanelDatas = DB::table('ClientPriceList')
            ->select('Panel')
            ->where('ClientID', '=', $clientID)
            ->where('GoodsID', '=', $model)
            ->get()->toArray();

        // Remove null Data
        foreach ( $PanelDatas as $key => $panel ) {
            if ( empty($panel) ) unset($PanelDatas[$key]);
        }

        return response()->json($PanelDatas, 200);
    }

    /**
     * Get Border from user selection
     *
     * @param \Illuminate\Http\Request $request
     * @return json
     */
    public function getBorder ( Request $request )
    {
        $model = ( $request->all() )['model'];
        $request->session()->put('model', $model);

        // Get clientID
        $clientID = $request->session()->get('clientID');

        // JUST FOR TESTING!!!!!
        // $clientID='ON053';

        // Load Border Data
        $BorderDatas = DB::table('ClientPriceList')
            ->select('Border')
            ->where('ClientID', '=', $clientID)
            ->where('GoodsID', '=', $model)
            ->get()->toArray();

        // Remove null Data
        foreach ( $BorderDatas as $key => $border ) {
            if ( empty($border) ) unset($BorderDatas[$key]);
        }

        return response()->json($BorderDatas, 200);
    }

    /**
     * Add user selection to Cart (Session)
     *
     * @param \Illuminate\Http\Request $request
     * @return json
     */
    public function addToCart ( Request $request )
    {
        $data = $request->all();
        $cart = $request->session()->has('cart') ? $request->session()->get('cart') : array();

        // Add to session
        array_push( $cart, $data );
        $request->session()->put('cart', $cart);

        return true;
    }

    /**
     * Remove user selection from Cart (Session)
     *
     * @param \Illuminate\Http\Request $request
     * @return json
     */
    public function removeFromCart ( Request $request )
    {
        $data = $request->all();
        if ( in_array( $data, $cart=$request->session()->get('cart') ) ) {
            $key = array_search( $data, $cart );
            unset( $cart[$key] );
            $request->session()->put('cart', $cart);
        }

        return true;
    }

    /**
     * Send user Order to DB
     *
     * @param \Illuminate\Http\Request $request
     * @return json
     */
    public function sendOrder ( Request $request )
    {
        $data = $request->session()->has('cart') ? $request->session()->get('cart') : null;
        if ( !empty($data) ) {
            // Save to ClientOrder
            $ClientOrderID = DB::table('ClientOrder')->insertGetId([
                'ClientID'  => $request->session()->get('clientID'),
                'OrderDate' => date('Y-m-d'),
                'OrderTime' => date('H:i:s'),
                'IP'        => $request->ip()
            ]);

            foreach ( $data as $item ) {
                // Save to ClientOrderDetail
                $clientOrderDetailID = DB::table('ClientOrderDetail')->insertGetId([
                    'ClientOrderID' => $ClientOrderID,
                    'GoodsID'       => $item['model'],
                    'DeliveryDate'  => $item['delivery_date'],
                    'PanelID'       => $item['panel'],
                    'BorderID'      => $item['border'],
                    'Size'          => $item['size'],
                    'Quantity'      => $item['quantity'],
                    'Note'          => $item['special_order']
                ]);

                // Save to MakeToday
                DB::table('MakeToday')->insert([
                    'ClientID'      => $request->session()->get('clientID'),
                    'GoodsID'       => $item['model'],
                    'DeliveryDate'  => $item['delivery_date'],
                    'PanelID'       => $item['panel'],
                    'BorderID'      => $item['border'],
                    'Size'          => $item['size'],
                    'Qty'           => $item['quantity'],
                    'Note_'         => $item['special_order'],
                    'ClientOrderDetailID' => $clientOrderDetailID
                ]);
            }
        }

        $request->session()->forget('cart');

        return true;
    }

    /**
     * Get Client Order History
     *
     * @param \Illuminate\Http\Request $request
     * @return View
     */
    public function history ( Request $request )
    {
        $clientID = $request->session()->get('clientID');

        // JUST FOR TESTING!!!!!
        // $clientID='ON053';

        $orderHistory = DB::table('ClientOrderDetail')
                    ->join('ClientOrder', 'ClientOrderDetail.ClientOrderID', '=', 'ClientOrder.ClientOrderID')
                    ->select('ClientOrderDetail.Quantity', 'ClientOrderDetail.Size', 'ClientOrderDetail.GoodsID', 'ClientOrderDetail.PanelID', 'ClientOrderDetail.BorderID', 'ClientOrderDetail.Note', 'ClientOrderDetail.DeliveryDate')
                    ->where('ClientOrder.ClientID', '=', $clientID)
                    ->orderBy('OrderDate', 'desc')
                    ->orderBy('OrderTime', 'desc')
                    ->limit(2)
                    ->get()
                    ->map(function ( $item ) {
                        return (array) $item;
                    })
                    ->all();

        return view('history', compact('orderHistory'));
    }
}
