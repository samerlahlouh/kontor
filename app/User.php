<?php

namespace Educators;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'user_name', 'password', 'type', 'created_by_user_id', 'mobile', 'group_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function get_users_table($group_id=''){
        $user_type = Auth::user()->type;

        $users = DB::table("users")
            ->select('id',
                    "is_active as is_active_hidden",
                    "name",
                    "email",
                    "mobile",
                    "type",
                    "balance",
                    "credit")
            ->where("created_by_user_id", Auth::user()->id);

        if($group_id)
            $users->where("group_id", $group_id);

        $users->orderBy('is_active');

        return $users->get();
    }

    public function get_users_table_with_extra_column($group_id=''){
        $user_type = Auth::user()->type;

        $users = DB::table("users")
            ->leftJoin('groups', 'groups.id', '=', 'users.group_id')
            ->select('users.id',
                "users.is_active as is_active_hidden",
                "users.name",
                "users.email",
                "users.mobile",
                "users.type",
                "groups.name as group_name",
                "users.balance",
                "users.credit",
                "users.is_checking_free as is_checking_free_hidden")
            ->where("users.created_by_user_id", Auth::user()->id);

        if($group_id)
            $users->where("group_id", $group_id);

        $users->orderBy('is_active');

        return $users->get();
    }

    public function get_all_users_table_with_extra_column(){
        $users = DB::table("users")
            ->leftJoin('users as created_by_users', 'created_by_users.id', '=', 'users.created_by_user_id')
            ->select('users.id',
                "users.is_active as is_active_hidden",
                "users.name",
                "created_by_users.name as created_by_user_name",
                "users.email",
                "users.mobile",
                "users.type",
                "users.balance",
                "users.credit",
                "users.is_checking_free as is_checking_free_hidden")
            ->where("users.type", '<>', 'admin');

        $users->orderBy('users.is_active');

        return $users->get();
    }
}
