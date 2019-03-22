<?php

namespace Educators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Auth;

class Charging extends Model
{
    protected $table = 'chargings';
    protected $fillable = [
        'user_id', 'type', 'status', 'amount', 'request_date', 'response_date', 'notes', 'balance_before', 'balance_after'
    ];

    public function get_chargings_table(){
        $chargings = DB::table("chargings")
            ->leftJoin('users', 'users.id', '=', 'chargings.user_id')
            ->select('chargings.id',
                    "users.name as user",
                    DB::raw("(CASE chargings.type 
                                WHEN 'eft' THEN '".__('chargings_lng.eft')."' 
                                WHEN 'cash' THEN '".__('chargings_lng.cash')."' 
                                WHEN 'credit' THEN '".__('chargings_lng.credit')."'
                                WHEN 'pay_off' THEN '".__('chargings_lng.pay_off')."'
                            END) AS type"),
                    DB::raw("(CASE chargings.status 
                                WHEN 'in_waiting' THEN '".__('chargings_lng.in_waiting')."' 
                                WHEN 'accepted' THEN '".__('chargings_lng.accepted')."' 
                                WHEN 'rejected' THEN '".__('chargings_lng.rejected')."'
                            END) AS status"),
                    "chargings.amount",
                    "chargings.balance_before",
                    "chargings.balance_after",
                    DB::raw('DATE(`chargings`.`request_date`) as request_date'),
                    DB::raw('DATE(`chargings`.`response_date`) as response_date'),
                    'chargings.notes'
                    )
            ->get();

        return $chargings;
    }

    public function get_regular_chargings_table($user_id){
        $packets = DB::table("chargings")
            ->select('id',
                    "status as status_hidden",
                    DB::raw("(CASE type 
                                WHEN 'eft' THEN '".__('chargings_lng.eft')."' 
                                WHEN 'cash' THEN '".__('chargings_lng.cash')."' 
                                WHEN 'credit' THEN '".__('chargings_lng.credit')."'
                                WHEN 'pay_off' THEN '".__('chargings_lng.pay_off')."'
                            END) AS type"),
                    DB::raw("(CASE status 
                                WHEN 'in_waiting' THEN '".__('chargings_lng.in_waiting')."' 
                                WHEN 'accepted' THEN '".__('chargings_lng.accepted')."' 
                                WHEN 'rejected' THEN '".__('chargings_lng.rejected')."'
                            END) AS status"),
                    "amount",
                    "balance_before",
                    "balance_after",
                    DB::raw('DATE(`request_date`) as request_date'),
                    DB::raw('DATE(`response_date`) as response_date'),
                    "notes"
                    )
            ->where("user_id", $user_id)
            ->get();

        return $packets;
    }
}
