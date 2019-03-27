<?php

namespace Educators\Http\Controllers;

use Illuminate\Http\Request;
use Educators\Operator;
use Educators\Packet;
use View;

class OperatorController extends Controller
{
    public function __construct(){
        $this->operator_model = new Operator();
    }

    public function index(){
        View::share('page_js', 'operators');
        $operators_from_db = $this->getEnumValues('packets', 'operator');

        $operators = [];
        foreach ($operators_from_db as $operator_from_db)
            array_push($operators, [$operator_from_db => $operator_from_db]);
            
        $operators_cols = [
            __('operators_lng.operator'),
        ];

        return view('operators', [
                                    'operators' => $operators,
                                    'operators_cols' => $operators_cols,
                                 ]);
    }

    public function store(Request $request){
        $this->is_validate($request);

        $operator   = $request->input('operator');
        $post_type  = $request->input('post_type');

        $operators = $this->getEnumValues('packets', 'operator');
        if($post_type == 'add')
            $this->operator_model->add_value_to_operators_field($operators, $operator);
        elseif($post_type == 'edit'){
            $old_operator   = $request->input('old_operator');
            $packets = Packet::where('operator', $old_operator)->get();
            if( count($packets) > 0 )
                return redirect("/operators")->with('error', __('operators_lng.this_element_used_warning'));        
            $operators[$old_operator] = $operator; 
            $this->operator_model->update_operators_field($operators);
        }
        
        return redirect("/operators")->with('success', __('main_lng.done_successfully'));
    }

    public function destroy($operator){
        $packets = Packet::where('operator', $operator)->get();
        if( count($packets) > 0 )
            return redirect("/operators")->with('error', __('operators_lng.this_element_used_warning'));    

        $operators = $this->getEnumValues('packets', 'operator');
        unset($operators[$operator]);
        $this->operator_model->update_operators_field($operators);

        return redirect("/operators")->with('success', __('main_lng.done_successfully'));
    }

    public function is_validate($request){
        $rules = array(
            'operator'  =>'required',
        );
        $this->validate($request ,$rules);
    }
}
