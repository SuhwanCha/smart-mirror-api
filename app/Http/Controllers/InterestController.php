<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class InterestController extends Controller
{
    public function get($uid)
    {
        $data = DB::table('user_interests')->select('interest')->where('userId', '=', $uid)->get();
        $arr = array();
        if (count($data)) {
            foreach ($data as $v) {
                array_push($arr, $v->interest);
            }
        }
        return response()->json($arr);
    }

    public function delete(Request $r)
    {
        DB::table('user_interests')->where([
            ['userId', '=', $r->input('uid')],
            ['interest', '=', $r->input('interest')]
        ])->delete();
    }

    public function put(Request $r)
    {
        DB::table('user_interests')->insert([
            'userId' => $r->input('uid'),
            'interest' => $r->input('interest')
        ]);
    }
}
