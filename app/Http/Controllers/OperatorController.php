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

    // ---------------------------------------- Indexes --------------------------------------------------//
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

    public function index_types($operator){
        View::share('page_js', 'operator_types');

        $operator_types = get_operator_types($operator);
        $all_types = get_all_types();
        $available_types = array_diff($all_types, $operator_types);
        $types = [];
        foreach ($operator_types as $key=>$operator_type){
            $type_data = [];
            $type_data[$key] = $key;
            $type_data['real_type_name'] = $operator_type;
            array_push($types,  $type_data);
        }

        $types_cols = [
            __('operators_lng.type'),
            __('operators_lng.real_type_name'),
        ];

        return view('operator_types', [
            'types'         => $types,
            'types_cols'    => $types_cols,
            'available_types'     => $available_types,
            'operator'      => $operator
        ]);
    }

    // ---------------------------------------- Actions --------------------------------------------------//
    public function store(Request $request){
        $this->is_validate($request);

        $is_api = $request->input('is_api');
        $operator   = $request->input('operator');
        $post_type  = $request->input('post_type');
        $operator_data['api_user_name']  = $request->input('api_user_name');
        $operator_data['api_password']  = $request->input('api_password');
        $operator_data['api_operator']  = $request->input('api_operator');
        $operator_data['is_api'] = isset($is_api);
        $operator_data['types'] = [];

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

    public function store_operator_type(Request $request){
        $this->is_validate_operator_type($request);

        $type   = $request->input('type');
        $post_type  = $request->input('post_type');
        $operator  = $request->input('operator');

        if($post_type == 'add')
            $this->add_and_del_type_in_operator($operator, $type, 'add');

        return redirect("/operator_types/$operator")->with('success', __('main_lng.done_successfully'));
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

    public function destroy_operator_type($data){
        $data = explode('_', $data);

        $this->add_and_del_type_in_operator($data[0], $data[1], 'del');

        return redirect("/operator_types/".$data[0])->with('success', __('main_lng.done_successfully'));
    }

    // ---------------------------------------- Functions --------------------------------------------------//
    public function is_validate($request){
        $rules = array(
            'operator'      =>'required',
            'api_user_name' =>'required',
            'api_password'  =>'required',
            'api_operator'  =>'required',
        );
        $this->validate($request ,$rules);
    }

    public function is_validate_operator_type($request){
        $rules = array(
            'type'      =>'required',
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

    private function add_and_del_type_in_operator($operator, $type, $request_type){
        $variables_file_path = public_path() . DIRECTORY_SEPARATOR . 'variables.json';
        $json = get_app_variables();

        if($request_type == 'add')
            $json['operators'][$operator]['types'][$type] = $json['types'][$type];
        elseif ($request_type == 'del')
            unset($json['operators'][$operator]['types'][$type]);

        $formattedData = json_encode($json);
        $handle = fopen($variables_file_path,'w+');
        fwrite($handle,$formattedData);
        fclose($handle);
    }
}
