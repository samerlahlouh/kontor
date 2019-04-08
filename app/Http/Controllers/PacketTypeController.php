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
        foreach ($types_from_db as $type_from_db)
            array_push($packets_types, [$type_from_db => $type_from_db]);
            
        $packets_types_cols = [
            __('packets_types_lng.type'),
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

        $types = $this->getEnumValues('packets', 'type');
        if($post_type == 'add')
            $this->packet_type_model->add_value_to_types_field($types, $type);
        elseif($post_type == 'edit'){
            $old_type   = $request->input('old_type');
            $packets = Packet::where('type', $old_type)->get();
            if( count($packets) > 0 )
                return redirect("/types")->with('error', __('packets_types_lng.this_element_used_warning'));        
            $types[$old_type] = $type; 
            $this->packet_type_model->update_types_field($types);
        }
        
        return redirect("/packets_types")->with('success', __('main_lng.done_successfully'));
    }

    public function destroy($type){
        $packets = Packet::where('type', $type)->get();
        if( count($packets) > 0 )
            return redirect("/packets_types")->with('error', __('packets_types_lng.this_element_used_warning'));    

        $types = $this->getEnumValues('packets', 'type');
        unset($types[$type]);
        $this->packet_type_model->update_types_field($types);

        return redirect("/packets_types")->with('success', __('main_lng.done_successfully'));
    }

    public function is_validate($request){
        $rules = array(
            'type'  =>'required',
        );
        $this->validate($request ,$rules);
    }
}
