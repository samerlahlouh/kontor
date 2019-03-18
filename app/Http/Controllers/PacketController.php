<?php

namespace Educators\Http\Controllers;

use Illuminate\Http\Request;
use Educators\Packet;
use View;

class PacketController extends Controller
{
    public function __construct()
    {
        $this->packet_modal = new Packet();
    }

    public function index()
    {
        View::share('page_js', 'packets');
        $packets = $this->packet_modal->get_packets_table();
        $operators = $this->getEnumValues('packets', 'operator');
        $types = $this->getEnumValues('packets', 'type');
        $is_global = [__('main_lng.private'), __('main_lng.global')];
        $is_teens = [__('main_lng.no'), __('main_lng.yes')];
        $cols = [
            'id',
            __('packets_lng.operator'),
            __('packets_lng.sms'),
            __('packets_lng.minutes'),
            __('packets_lng.internet'),
            __('packets_lng.type'),
            __('packets_lng.price'),
            __('packets_lng.is_global'),
            __('packets_lng.is_teens'),
        ];

        return view('packets', ['packets' => $packets,
                                'cols' => $cols,
                                'operators' => $operators,
                                'types' => $types,
                                'is_global' => $is_global,
                                'is_teens' => $is_teens]);
    }

   
    public function store(Request $request)
    {
        $this->is_validate($request);

        $id = $request->input('id');

        $data = $request->all();
        unset($data['id'], $data['_token']);
        $data['is_global'] -= 1; 
        $data['is_teens'] -= 1;

        if($id){
            $packet = Packet::find($id);
            $packet->fill($data);
            $packet->save();
        }else
        Packet::create($data);

        return redirect("/packets")->with('success', __('main_lng.done_successfully'));
    }

    public function destroy($id)
    {
        $packet = Packet::find($id);
        $packet->delete();
        return redirect("/packets")->with('success',  __('main_lng.done_successfully'));
    }


    public function is_validate($request){
        $rules = array(
            'operator'  =>'required',
            'sms'       =>'required',
            'minutes'   =>'required',
            'internet'  =>'required',
            'type'      =>'required',
            'is_global' =>'required',
            'is_teens'  =>'required',
        );
        $this->validate($request ,$rules);
    }

    // Get packet by id
    public function get_packet(Request $request){
        $id = $request->id;
        $packet = Packet::where('id', $id)->get()[0];
        return response()->json($packet); 
    }
}
