<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    // login page
    public function create()
    {
        return view('sessions.create');
    }

    // perform the login action
    public function store(Request $request)
    {
        // returned an array with inputs if validation pass
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required',
        ]);

        // Auth::attempt(['email' => $email, 'password' => $password])
        // 接收一个数组来作为第一个参数，该参数提供的值将用于寻找数据库中的用户数据
        // 第二个参数为是否为用户开启『记住我』功能的布尔值
        if (Auth::attempt($credentials, $request->has('remember')))
        {
            if (Auth::user()->activated)
            {
                session()->flash('success', '欢迎回来！');
                $fallback = route('users.show', Auth::user());
                return redirect()->intended($fallback);
                // return redirect()->route('users.show', [Auth::user()]);
            }
            else
            {
                Auth::logout();
                session()->flash('warning', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');
            }

        }
        else
        {
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            // withInput() works for 'old()' in the view template
            return redirect()->back()->withInput();
        }

        return;
    }

    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '您已成功退出！');
        return redirect('login'); //what is difference from route('login')?
    }
}
