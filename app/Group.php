<?php

namespace Educators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Auth;

class Group extends Model
{
    protected $table = 'groups';

    protected $fillable = [
        'created_by_user_id',
        'name',
        'description'
    ];

    public function get_groups_table(){
        $groups = Auth::user()->type;

        $groups = DB::table("groups")
            ->select('id',
                "name",
                "description"
            )
            ->where("created_by_user_id", Auth::user()->id);

        return $groups->get();
    }
}
