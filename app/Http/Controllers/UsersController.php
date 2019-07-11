<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        //return a User object if succeed
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');

        // 注意这里是一个『约定优于配置』的体现，此时 $user 是 User 模型对象的实例。
        // route() 方法会自动获取 Model 的主键，也就是数据表 users 的主键 id，以上代码等同于：
        // redirect()->route('users.show', [$user->id]);
        return redirect()->route('users.show', [$user]);
    }
}
