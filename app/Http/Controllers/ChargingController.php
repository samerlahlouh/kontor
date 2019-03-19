<?php

namespace Educators\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Educators\Charging;
use Educators\User;
use View;
use Auth;

class ChargingController extends Controller
{
    public function __construct(){
        $this->charging_modal = new Charging();
    }
    
    public function index(){
        View::share('page_js', 'chargings');
        $chargings = $this->charging_modal->get_chargings_table();
        
        $select_users       = [];
        $select_types       = [];
        $select_statuses    = [];

        $users = User::select("id", 'name')->where('created_by_user_id', Auth::user()->id)->get();
        foreach ($users as $user)
            $select_users[$user['id']] = $user["name"];

        $types = $this->getEnumValues('chargings', 'type');
        foreach ($types as $type)
            $select_types[$type] = __("chargings_lng.$type");

        $statuses = $this->getEnumValues('chargings', 'status');
        foreach ($statuses as $status)
            $select_statuses[$status] = __("chargings_lng.$status");

        $cols = [
            'id',
            __('chargings_lng.user'),
            __('chargings_lng.type'),
            __('chargings_lng.status'),
            __('chargings_lng.amount'),
            __('chargings_lng.request_date'),
            __('chargings_lng.response_date'),
            __('chargings_lng.notes'),
        ];

        return view('chargings', ['chargings'       => $chargings,
                                'cols'              => $cols,
                                'select_types'      => $select_types,
                                'select_statuses'   => $select_statuses,
                                'select_users'      => $select_users]);
    }

    //------------------------------------------Actions--------------------------------------------//
    public function store(Request $request){
        $this->is_validate($request);

        $id = $request->input('id');

        $newData = $request->all();
        unset($newData['id'], $newData['_token']);

        if($id){
            $charging = Charging::find($id);

            $user = User::find($newData['user_id']);
            if($newData['status'] == 'accepted' && $charging['status'] == 'accepted')
                $user['balance'] = $user['balance'] + ($newData['amount'] - $charging['amount']);
            elseif($newData['status'] == 'accepted' && $charging['status'] != 'accepted')
                $user['balance'] = $user['balance'] + $newData['amount'];
            elseif($newData['status'] != 'accepted' && $charging['status'] == 'accepted')
                $user['balance'] = $user['balance'] - $charging['amount'];

            $charging->fill($newData);
            $user->save();

            $charging->save();
        }else{
            if($newData['status'] == 'accepted'){
                $user = User::find($newData['user_id']);
                $user['balance'] = $user['balance'] + $newData['amount'];
                $user->save();
            }

            Charging::create($newData);
        }

        return redirect("/chargings")->with('success', __('main_lng.done_successfully'));
    }

    public function destroy($id){
        $charging = Charging::find($id);

        if($charging['status'] == 'accepted'){
            $user = User::find($charging['user_id']);
            $user['balance'] = $user['balance'] - $charging['amount'];
            $user->save();
        }

        $charging->delete();
        return redirect("/chargings")->with('success',  __('main_lng.done_successfully'));
    }

    //------------------------------------------Functions--------------------------------------------//
    public function is_validate($request){
        $rules = array(
            'user_id'           =>'required',
            'type'              =>'required',
            'status'            =>'required',
            'amount'            =>'required',
            'request_date'      =>'required',
        );
        $this->validate($request ,$rules);
    }

    // Get charging by id
    public function get_charging(Request $request){
        $id = $request->id;
        $charging = Charging::where('id', $id)->select('*',
                                                        DB::raw('DATE(`request_date`) as request_date'),
                                                        DB::raw('DATE(`response_date`) as response_date'))->get()[0];
        return response()->json($charging); 
    }
}
