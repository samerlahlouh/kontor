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
        'name', 'email', 'user_name', 'password', 'type', 'created_by_user_id', 'mobile'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function get_users_table(){
        $users = DB::table("users")
            ->select('id',
                    "is_active as is_active_hidden",
                    "name",
                    "email",
                    "mobile",
                    "type",
                    "balance",
                    "credit")
            ->where("created_by_user_id", Auth::user()->id)
            ->orderBy('is_active')
            ->get();

        return $users;
    }
}
