<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        // returned an array with inputs if validation pass
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required',
        ]);

        // Auth::attempt(['email' => $email, 'password' => $password])
        // 接收一个数组来作为第一个参数，该参数提供的值将用于寻找数据库中的用户数据
        if (Auth::attempt($credentials))
        {
            session()->flash('success', '欢迎回来！');
            return redirect()->route('users.show', [Auth::user()]);
        }else{
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            // withInput() works for 'old()' in the view template
            return redirect()->back()->withInput();
        }

        return;
    }
}
