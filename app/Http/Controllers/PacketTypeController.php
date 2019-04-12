<?php

namespace Educators\Http\Controllers;

use Illuminate\Http\Request;
use Educators\Packet_Type;
use Educators\Packet;
use View;

class PacketTypeController extends Controller
{
    public function __construct(){
        $this->packet_type_model = new Packet_Type();
    }

    public function index(){
        View::share('page_js', 'packets_types');
        $types_from_db = $this->getEnumValues('packets', 'type');

        $packets_types = [];
        foreach ($types_from_db as $type_from_db){
            $type_data = [];
            $type_data[$type_from_db] = $type_from_db;
            $type_data['real_type_name'] = get_real_type_name($type_from_db);
            array_push($packets_types,  $type_data);
        }

        $packets_types_cols = [
            __('packets_types_lng.type'),
            __('packets_types_lng.real_type_name'),
        ];

        return view('packets_types', [
                                    'packets_types'         => $packets_types,
                                    'packets_types_cols'    => $packets_types_cols,
                                 ]);
    }

    public function store(Request $request){
        $this->is_validate($request);

        $type   = $request->input('type');
        $post_type  = $request->input('post_type');
        $real_type_name  = $request->input('real_type_name');

        $types = $this->getEnumValues('packets', 'type');
        if($post_type == 'add'){
            $this->packet_type_model->add_value_to_types_field($types, $type);
            $this->add_and_edit_type_to_json_file($type, $real_type_name);
        }elseif($post_type == 'edit'){
            $old_type   = $request->input('old_type');
            $packets = Packet::where('type', $old_type)->get();
            if( count($packets) > 0 )
                return redirect("/types")->with('error', __('packets_types_lng.this_element_used_warning'));        
            $types[$old_type] = $type; 
            $this->packet_type_model->update_types_field($types);
            $this->add_and_edit_type_to_json_file($old_type, $real_type_name);
        }
        
        return redirect("/packets_types")->with('success', __('main_lng.done_successfully'));
    }

    public function destroy($type){
        $packets = Packet::where('type', $type)->get();
        if( count($packets) > 0 )
            return redirect("/packets_types")->with('error', __('packets_types_lng.this_element_used_warning'));

        $this->delete_type_from_json_file($type);
        $types = $this->getEnumValues('packets', 'type');
        unset($types[$type]);
        $this->packet_type_model->update_types_field($types);

        return redirect("/packets_types")->with('success', __('main_lng.done_successfully'));
    }

    public function is_validate($request){
        $rules = array(
            'type'              =>'required',
            'real_type_name'    =>'required',
        );
        $this->validate($request ,$rules);
    }

    private function add_and_edit_type_to_json_file($type, $real_type_name){
        $variables_file_path = public_path() . DIRECTORY_SEPARATOR . 'variables.json';
        $data = file_get_contents ($variables_file_path);
        $json = json_decode($data, true);

        $json['types'][$type] = $real_type_name;

        $formattedData = json_encode($json);
        $handle = fopen($variables_file_path,'w+');
        fwrite($handle,$formattedData);
        fclose($handle);
    }

    private function delete_type_from_json_file($type){
        $variables_file_path = public_path() . DIRECTORY_SEPARATOR . 'variables.json';
        $data = file_get_contents ($variables_file_path);
        $json = json_decode($data, true);

        unset($json['types'][$type]);

        $formattedData = json_encode($json);
        $handle = fopen($variables_file_path,'w+');
        fwrite($handle,$formattedData);
        fclose($handle);
    }
}
