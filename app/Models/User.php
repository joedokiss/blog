<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));

        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    public function feed()
    {
        $user_ids = $this->followings->pluck('id')->toArray();

        array_push($user_ids, $this->id);

        return Status::whereIn('user_id', $user_ids)
                            ->with('user')
                            ->orderBy('created_at', 'desc');

        // return $this->statuses()->orderBy('created_at', 'desc');
    }

    // 方法会在用户模型类完成初始化之后进行加载
    public static function boot()
    {
        parent::boot();

        static::creating(function($user){
            $user->activation_token = str_random(30);
        });
    }

    // 获取粉丝关系列表
    // belongsToMany 方法的第三个参数 user_id 是定义在关联中的模型外键名，而第四个参数 follower_id 则是要合并的模型外键名。
    public function followers()
    {
        // 当 user_id 为某个值时，合并 follower_id 之后的数据
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    // 获取用户关注人列表
    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    public function follow($user_ids)
    {
        if (!is_array($user_ids))
        {
            $user_ids = compact('user_ids');
        }

        $this->followings()->sync($user_ids, false);
    }

    public function unfollow($user_ids)
    {
        if (!is_array($user_ids))
        {
            $user_ids = compact('user_ids');
        }

        $this->followings()->detach($user_ids);
    }

    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }
}
