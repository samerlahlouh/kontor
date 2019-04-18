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
        foreach ($operators_from_db as $operator_from_db){
            $operator_data = [];
            $operator_data[$operator_from_db] = $operator_from_db;
            $operator_data += get_operator_data($operator_from_db);
            $operator_data['is_api_hidden'] = $operator_data['is_api'];
            $operator_data['is_api'] = $operator_data['is_api']?__('main_lng.yes'):__('main_lng.no');
            array_push($operators,  $operator_data);
        }

        $operators_cols = [
            __('operators_lng.operator'),
            __('operators_lng.api_user_name'),
            __('operators_lng.api_password'),
            __('operators_lng.api_operator'),
            'is_api_hidden',
            __('operators_lng.is_api'),
        ];

        return view('operators', [
                                    'operators' => $operators,
                                    'operators_cols' => $operators_cols,
                                 ]);
    }

    public function store(Request $request){
        $this->is_validate($request);

        $is_api = $request->input('is_api');
        $operator   = $request->input('operator');
        $post_type  = $request->input('post_type');
        $operator_data['api_user_name']  = $request->input('api_user_name');
        $operator_data['api_password']  = $request->input('api_password');
        $operator_data['api_operator']  = $request->input('api_operator');
        $operator_data['is_api'] = isset($is_api);

        $operators = $this->getEnumValues('packets', 'operator');
        if($post_type == 'add'){
            $this->operator_model->add_value_to_operators_field($operators, $operator);
            $this->add_and_edit_operator_to_json_file($operator, $operator_data);
        }elseif($post_type == 'edit'){
            $old_operator   = $request->input('old_operator');
            $operators[$old_operator] = $operator;
            $this->operator_model->update_operators_field($operators);
            $this->add_and_edit_operator_to_json_file($old_operator, $operator_data);
        }

        return redirect("/operators")->with('success', __('main_lng.done_successfully'));
    }

    public function destroy($operator){
        $packets = Packet::where('operator', $operator)->get();
        if( count($packets) > 0 )
            return redirect("/operators")->with('error', __('operators_lng.this_element_used_warning'));    

        $this->delete_operator_from_json_file($operator);
        $operators = $this->getEnumValues('packets', 'operator');
        unset($operators[$operator]);
        $this->operator_model->update_operators_field($operators);

        return redirect("/operators")->with('success', __('main_lng.done_successfully'));
    }

    public function is_validate($request){
        $rules = array(
            'operator'      =>'required',
            'api_user_name' =>'required',
            'api_password'  =>'required',
            'api_operator'  =>'required',
        );
        $this->validate($request ,$rules);
    }

    private function add_and_edit_operator_to_json_file($operator, $operator_data){
        $operator_data = recursive_change_key($operator_data, array('api_user_name' => 'kod', 'api_password' => 'sifre', 'api_operator' => 'operator'));
        $variables_file_path = public_path() . DIRECTORY_SEPARATOR . 'variables.json';
        $data = file_get_contents ($variables_file_path);
        $json = json_decode($data, true);

        $json['operators'][$operator] = $operator_data;

        $formattedData = json_encode($json);
        $handle = fopen($variables_file_path,'w+');
        fwrite($handle,$formattedData);
        fclose($handle);
    }

    private function delete_operator_from_json_file($operator){
        $variables_file_path = public_path() . DIRECTORY_SEPARATOR . 'variables.json';
        $data = file_get_contents ($variables_file_path);
        $json = json_decode($data, true);

        unset($json['operators'][$operator]);

        $formattedData = json_encode($json);
        $handle = fopen($variables_file_path,'w+');
        fwrite($handle,$formattedData);
        fclose($handle);
    }
}
