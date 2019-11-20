<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class SearchController extends Controller {

 public function show(Request $r) {
  $format = 'https://dapi.kakao.com/v2/local/search/keyword.json?y=%f&x=%f&query=%s&sort=%s';
  $uid = $r->input('deviceId');
  DB::table('history')->insert([
   'deviceid' => $uid,
   'name' => $r->input('query'),
  ]);
  if ($r->input('sort') === null) {
   $sort = 1;
  } else {
   $sort = $r->input('sort');
  }
  $url = sprintf($format, $r->input('x'), $r->input('y'), urlencode($r->input('query')), $sort ? "accuracy" : "distance");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

  $headers = array();
  $headers[] = 'Authorization: KakaoAK ' . env('KAKAO_KEY');
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $result = curl_exec($ch);
  if (curl_errno($ch)) {
   echo 'Error:' . curl_error($ch);
  }
  curl_close($ch);
  $rawData = json_decode($result);
  $data = array();
  foreach ($rawData->documents as $value) {
   $temp = array(
    'x' => $value->x,
    'y' => $value->y,
    'name' => $value->place_name,
    'addr' => $value->road_address_name,
   );
   array_push($data, $temp);
  }
  return response()->json($data, 200, array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
  //   return response()->json($data, JSON_UNESCAPED_UNICODE);
 }

 public function showHistory($uid) {
  return response()->json(
   DB::table('history')->select('name as query', 'created_at')->where('deviceid', $uid)->get(),
   200, array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);

 }

 public function putFavorite(Request $r) {
  try {
   DB::table('bookmark')->insert([
    'x' => $r->input('x'),
    'y' => $r->input('y'),
    'name' => $r->input('name'),
    'deviceid' => $r->input('deviceId'),
    'address' => $r->input('address'),
   ]);
   echo "1";
  } catch (Exception $e) {
   echo "error";
  }
 }

 public function putHistory(Request $r) {
  try {
   DB::table('newHistory')->insert([
    'x' => $r->input('x'),
    'y' => $r->input('y'),
    'name' => $r->input('name'),
    'deviceid' => $r->input('deviceId'),
    'address' => $r->input('address'),
   ]);
   echo "1";
  } catch (Exception $e) {
   echo "error";
  }
 }

 public function getFavorite($uid) {
  return response()->json(
   DB::table('bookmark')->select('x', 'y', 'name', 'address', 'created_at')->where('deviceid', $uid)->get(),
   200, array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
 }

 public function showHistory2($uid) {
  return response()->json(
   DB::table('newHistory')->select('x', 'y', 'name as query', 'address', 'created_at')->where('deviceid', $uid)->get(),
   200, array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
 }
}
