<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class todo extends Controller
{
    public function delete(Request $r)
    {
        DB::table('todo')->where([
            ['userId', '=', $r->input('uid')],
            ['created_at', '=', $r->input('created_at')]
        ])->delete();
    }

    public function put(Request $r)
    {
        DB::table('todo')->insert([
            'userId' => $r->input('uid'),
            'created_at' => $r->input('created_at'),
            'text' => $r->input('text')
        ]);
    }

    public function check(Request $r)
    {
        DB::table('todo')->where([
            ['userId', '=', $r->input('uid')],
            ['created_at', '=', $r->input('created_at')]
        ])->update([
            'checked' => $r->input('checked')
        ]);
    }

    public function get($uid)
    {
        $data = DB::table('todo')->selectRaw('text, checked, created_at as date')->where('userId', $uid)->get();
        return response()->json($data);
    }
}
