<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class userController extends Controller
{
    public function put(Request $r)
    {
        DB::table('users')->insert(
            [
                'nickname' => $r->input('nickname'),
                'email' => $r->input('email'),
                'password' =>  $r->input('password')
            ]
        );
        return DB::table('users')->orderBy('id', 'desc')->first()->id;
    }

    public function get(Request $r)
    {
        try {
            return DB::table('users')->select('id')->where([
                ['email', '=', $r->input('email')],
                ['password', '=', $r->input('password')]
            ])->first()->id;
        } catch (\Throwable $th) {
            return 0;
        }
    }
}
