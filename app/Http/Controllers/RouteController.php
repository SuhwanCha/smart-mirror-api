<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RouteController extends Controller {
 public function show(Request $r) {
  $x1 = $r->input('x1');
  $x2 = $r->input('x2');
  $y1 = $r->input('y1');
  $y2 = $r->input('y2');
  $format = 'https://beta.map.naver.com/api/dir/findwalk?lo=ko&r=step&st=1&o=all&l=%f,%f,;%f,%f,&lang=ko';
  $url = sprintf($format, $x1, $y1, $x2, $y2);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  $result = curl_exec($ch);
  $rawData = json_decode($result)->routes[0];

  $route = $rawData->legs[0]->steps;
  // array_shift($route);
  // array_pop($route);
  // return response()->json($route, 200, array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);

  $summary = $rawData->summary;
  $data = array(
   'summary' => array(
    'distance' => $summary->distance,
    'duration' => $summary->duration,
    'routeLength' => count($route),
   ),
   'route' => array(),
  );
  $data['route'] = $this->pasrseWalk($route);

  return response()->json($data, 200, array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
 }

 private function pasrseWalk($arr) {
  $data = array();
  $x = 0;
  $y = 0;
  $i = 0;
  foreach ($arr as $v) {
   try {
    $lon1 = (double) $v->eye[0];
    $lon2 = (double) $v->lookAt[0];
    $lat1 = (double) $v->eye[1];
    $lat2 = (double) $v->lookAt[1];
    $a = log(tan($lat2 / 2 + M_PI / 4) / tan($lat1 / 2 + M_PI / 4));
    $lon = abs($lon1 - $lon2);
    $tan = (atan2($lon, $a) * 180 / M_PI);
    $tan *= ($lon2 - $lon1 < 0) ? -1 : 1;

   } catch (\Throwable $th) {
    $tan = 0;
   }

   $pathX = array();
   $pathY = array();
   foreach (explode(' ', $v->path) as $vv) {
    if (($vv)) {
     array_push($pathX, (double) explode(',', $vv)[1]);
     array_push($pathY, (double) explode(',', $vv)[0]);
    }
   }
   array_shift($pathX);
   array_shift($pathY);

   $temp = array(
    'x' => isset($arr[$i + 1]) ? $arr[$i + 1]->lng : 0,
    'y' => isset($arr[$i + 1]) ? $arr[$i + 1]->lat : 0,
    'direction' => $tan,
    'description' => $v->turnDesc,
    'distance' => $v->distance,
    'duration' => $v->duration,
    'pathLength' => count($pathX),
    'pathX' => $pathX,
    'pathY' => $pathY,
   );
   array_push($data, $temp);
   $i++;
  }
  array_shift($data);
  array_pop($data);

  return $data;
 }

 public function showBus(Request $r) {
  $x1 = $r->input('x1');
  $x2 = $r->input('x2');
  $y1 = $r->input('y1');
  $y2 = $r->input('y2');
  $format = 'https://beta.map.naver.com/api/dir/findpt?start=%f,%f,placeid=,name=1&goal=%f,%f,placeid=,name=1&crs=EPSG:4326&departureTime=%s&isStatic=null&mode=TIME&lang=ko';
  $url = sprintf($format, $x1, $y1, $x2, $y2, str_replace(' ', 'T', date("Y-m-d H:i:s", time() - date("Z"))));
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  $result = curl_exec($ch);
  $rawData = json_decode($result);

  foreach ($rawData->paths as $v) {
   if ($v->type == "BUS") {
    $path = $v;
    break;
   }
  }

  if (!isset($path)) {
   return "버스로만 갈 수 있는 방법이 없습니다.";
  }

  $data = array(
   'summary' => array(
    'departureTime' => explode('T', $path->departureTime)[1],
    'arrivalTime' => explode('T', $path->arrivalTime)[1],
    'duration' => $path->duration,
   ),
   'route' => array(),
  );
  foreach ($path->legs[0]->steps as $v) {
   if ($v->type == 'WALKING') {
    $temp = array(
     'type' => 'WALKING',
    );
    $temp = array_merge($temp, array(
     'route' => $this->pasrseWalk($v->walkpath->legs[0]->steps),
    ));

   } else if ($v->type == 'BUS') {
    $pathX = array();
    $pathY = array();
    foreach ($v->points as $vv) {
     if (($vv)) {
      array_push($pathX, (double) $vv->x);
      array_push($pathY, (double) $vv->y);
     }
    }
    $staion = array();
    foreach ($v->stations as $vv) {
     $url = 'http://bus.go.kr/xmlRequest/getStationByUid.jsp';
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_POST, 1);
     curl_setopt($ch, CURLOPT_POSTFIELDS,
      "strBusNumber=" . str_replace('-', '', $vv->displayCode));

     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

     $re = curl_exec($ch);
     $xml = simplexml_load_string($re, "SimpleXMLElement", LIBXML_NOCDATA);
     $json = json_encode($xml);

     $re = json_decode($json, true);

     try {
      $temp = array(
       'id' => $vv->displayCode,
       'name' => $vv->displayName,
       'x' => (double) isset($re['stationList'][0]) ? $re['stationList'][0]['gpsX'] : $re['stationList']['gpsX'],
       'y' => (double) isset($re['stationList'][0]) ? $re['stationList'][0]['gpsY'] : $re['stationList']['gpsY'],
      );

     } catch (\Throwable $th) {
     }

     array_push($staion, $temp);
    }
    try {
     $busCongestion = $v->arrivals[0]->items[0]->congestion->desc;
     $remainingTime = $v->arrivals[0]->items[0]->remainingTime;
    } catch (\Throwable $e) {
     $busCongestion = null;
     $remainingTime = null;
    }

    $temp = array(
     'type' => $v->type,
     'description' => $v->instruction,
     'distance' => $v->distance,
     'duration' => $v->duration,
     'busNumber' => $v->routes[0]->name,
     'busColor' => $v->routes[0]->type->color,
     'busCongestion' => $busCongestion,
     'remainingTime' => $remainingTime,
     'pathLength' => count($pathY),
     'pathX' => $pathX,
     'pathY' => $pathY,
     'stationLength' => count($staion),
     'station' => $staion,
    );

   }
   array_push($data['route'], $temp);

  }

  return response()->json($data, 200, array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
 }

 public function direction(Request $r) {
  $lon2 = (double) $r->input('x2');
  $lon1 = (double) $r->input('x1');
  $lat1 = (double) $r->input('y1');
  $lat2 = (double) $r->input('y2');
  $a = log(tan($lat2 / 2 + M_PI / 4) / tan($lat1 / 2 + M_PI / 4));
  $lon = abs($lon1 - $lon2);
  $bear = (atan2($lon, $a) * 180 / M_PI);
  $bear *= ($lon2 - $lon1 < 0) ? -1 : 1;
  return ($bear);
 }

}
