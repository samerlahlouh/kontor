<?php

namespace Educators\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ChargingStoreRequest;
use Educators\Charging;
use Educators\User;
use Illuminate\Validation\ValidationException;
use View;
use Auth;
use Carbon\Carbon;

class ChargingController extends Controller
{
    public function __construct(){
        $this->charging_model = new Charging();
    }

    //------------------------------------------ Indexes --------------------------------------------//
    public function index(){
        View::share('page_js', 'chargings');
        $chargings = $this->charging_model->get_chargings_table();

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
            __('chargings_lng.balance_before'),
            __('chargings_lng.balance_after'),
            __('chargings_lng.request_date'),
            __('chargings_lng.response_date'),
            __('chargings_lng.notes'),
        ];

        return view('chargings', ['chargings'         => $chargings,
                                        'cols'              => $cols,
                                        'select_types'      => $select_types,
                                        'select_statuses'   => $select_statuses,
                                        'select_users'      => $select_users]);
    }

    public function index_regular_chargings(){
        View::share('page_js', 'regular_chargings');
        $regular_chargings = $this->charging_model->get_regular_chargings_table(Auth::user()->id);

        $select_types = [];
        $types = $this->getEnumValues('chargings', 'type');
        unset($types['pay_off']);
        foreach ($types as $type)
            $select_types[$type] = __("chargings_lng.$type");

        $cols = [
            'id',
            'status_hidden',
            __('chargings_lng.type'),
            __('chargings_lng.status'),
            __('chargings_lng.amount'),
            __('chargings_lng.balance_before'),
            __('chargings_lng.balance_after'),
            __('chargings_lng.request_date'),
            __('chargings_lng.response_date'),
            __('chargings_lng.notes')
        ];

        return view('regular_chargings', [
                                        'regular_chargings'     => $regular_chargings,
                                        'cols'                  => $cols,
                                        'select_types'          => $select_types
                                    ]);
    }

    //------------------------------------------Actions--------------------------------------------//
    public function store(Request $request){
        $current_user = Auth::user();
        $newData = $request->all();
        if(!$newData['request_date'])
            $newData['request_date'] = Carbon::now();
        $this->add_is_validate($request);
        unset($newData['id'], $newData['_token']);
        $user = User::find($newData['user_id']);

        if($newData['type'] != 'pay_off'){
            $newData['balance_before'] = $user->balance;
            $newData['balance_after'] = $newData['balance_before'] + $newData['amount'];

            if($newData['status'] == 'accepted'){
                $user->balance += $newData['amount'];
                if($newData['type'] == 'credit')
                    $user->credit += $newData['amount'];

                if($current_user->type == 'agent')
                    $current_user->balance -= $newData['amount'];
            }
        }else{
            $newData['status'] = 'accepted';
            $newData['balance_before'] = $user->balance;
            $newData['balance_after'] = $user->balance;

            if($newData['amount'] > $user->credit){
                $error = ValidationException::withMessages([
                    'amount' => __("chargings_lng.pay_off_more_than_balance_warning")
                ]);
                throw $error;
            }
            $user->credit -= $newData['amount'];
        }

        if($current_user->type == 'agent' && $current_user->balance < 0){
            return redirect("/chargings")->with('error', __('chargings_lng.balance_is_not_enough_warning'));
        }

        $current_user->save();
        $user->save();
        Charging::create($newData);

        return redirect("/chargings")->with('success', __('main_lng.done_successfully'));
    }

    public function store_regular_charing(Request $request){

        $this->is_regular_validate($request);

        $newData = $request->all();
        unset($newData['id'], $newData['_token']);
        $userId = Auth::user()->id;
        $user = User::find($userId);
        $parent_user = User::find($user->created_by_user_id);

        $newData['user_id'] = $userId;
        $newData['status'] = 'in_waiting';
        $newData['balance_before'] = $user->balance;
        $newData['balance_after'] = $user->balance + $newData['amount'];
        $newData['request_date'] = Carbon::now();

        Charging::create($newData);

        if($parent_user->type != 'agent'){
            $msg_title = 'يوجد طلب تحويل مبلغ من :'.$user->name;
            $msg_body = $newData['amount'];
            sendMessage($msg_title, $msg_body);
        }

        return redirect("/regular_chargings")->with('success', __('main_lng.done_successfully'));
    }

    public function update(Request $request){
        $id = $request->input('id');
        $newData = $request->all();
        $this->edit_is_validate($request);
        unset($newData['id'], $newData['_token']);

        $charging = Charging::find($id);
        $charging->fill($newData);
        $charging->save();

        return redirect("/chargings")->with('success', __('main_lng.done_successfully'));
    }

    public function destroy($id){
        $current_user = Auth::user();
        $charging = Charging::find($id);
        $user = User::find($charging->user_id);
        if($charging->status == 'accepted') {
            if ($charging->type != 'pay_off') {
                $user->balance -= $charging->amount;
                if ($charging->type == 'credit')
                    $user->credit -= $charging->amount;

                if($current_user->type == 'agent')
                    $current_user->balance += $charging->amount;
            }else{
                $user->credit += $charging->amount;

                if($current_user->type == 'agent')
                    $current_user->balance -= $charging->amount;
            }

        }

        $current_user->save();
        $user->save();
        $charging->delete();
        return redirect("/chargings")->with('success',  __('main_lng.done_successfully'));
    }

    //------------------------------------------Functions--------------------------------------------//
    public function add_is_validate($request){
        $rules = array(
            'user_id'           =>'required',
            'type'              =>'required',
            'status'            =>'required',
            'amount'            =>'required|not_in:0',
        );
        $this->validate($request ,$rules);
    }


    public function edit_is_validate($request){
        $rules = array(
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

    public function delete_charging(Request $request){
        $charging_id = $request->charging_id;
        $charging = Charging::find($charging_id);
        if($charging->status !== 'in_waiting'){
            return response()->json('error');
        }
        $charging->delete();
        return response()->json('success');
    }

    public function is_regular_validate($request){
        $rules = array(
            'type'      =>'required',
            'amount'    =>'required',
        );
        $this->validate($request ,$rules);
    }
}
