<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store'] // black list
        ]);
    }

    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * create and save a new user
     */
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

        Auth::login($user);

        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');

        // 注意这里是一个『约定优于配置』的体现，此时 $user 是 User 模型对象的实例。
        // route() 方法会自动获取 Model 的主键，也就是数据表 users 的主键 id，以上代码等同于：
        // redirect()->route('users.show', [$user->id]);
        return redirect()->route('users.show', [$user]);
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;

        if ($request->password)
        {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        // $user->update([
        //     'name' => $request->name,
        //     'password' => bcrypt($request->password),
        // ]);

        session()->flash('success', '个人资料更新成功！');

        // return redirect()->route('users.show', $user->id);
        return redirect()->route('users.show', $user);
    }
}
