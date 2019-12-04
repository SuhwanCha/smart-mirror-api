<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class bus extends Controller
{
    public function b1()
    {
        $url = 'https://map.naver.com/v5/api/transit/bus/stops/164323?lang=ko&caller=naver_map&output=json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $result = curl_exec($ch);
        return response()->json(json_decode($result));
    }
    public function b2()
    {
        $url = 'https://map.naver.com/v5/api/bus/arrival?lang=ko&stationId=164323&caller=pc_map';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $result = curl_exec($ch);
        return response()->json(json_decode($result));
    }
}
