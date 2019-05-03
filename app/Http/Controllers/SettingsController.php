<?php

namespace Educators\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $all_variables = get_app_variables();

        $app_title = $all_variables['app_title'];

        $send_notification_app_id = $all_variables['send_notification_app_id'];
        $send_notification_rest_api_key = $all_variables['send_notification_rest_api_key'];

        return view('settings', [
            'app_title'                         => $app_title,
            'send_notification_app_id'          => $send_notification_app_id,
            'send_notification_rest_api_key'    => $send_notification_rest_api_key
        ]);
    }

    public function update(Request $request)
    {
        $this->validator($request);

        $app_title = $request->input('app_title');
        $send_notification_app_id = $request->input('send_notification_app_id');
        $send_notification_rest_api_key = $request->input('send_notification_rest_api_key');

        $this->update_variables_in_json_file(['app_title', 'send_notification_app_id', 'send_notification_rest_api_key'],
                                             [$app_title, $send_notification_app_id, $send_notification_rest_api_key]);

        return redirect('/app_settings')->with('success', __('main_lng.done_successfully'));
    }

    public function validator($request)
    {
        $rules = array(
            'app_title' => 'required',
            'send_notification_app_id' => 'required',
            'send_notification_rest_api_key' => 'required',
        );
        $this->validate($request, $rules);
    }

    private function update_variables_in_json_file($variables, $variables_data){
        $variables_file_path = public_path() . DIRECTORY_SEPARATOR . 'variables.json';
        $data = file_get_contents ($variables_file_path);
        $json = json_decode($data, true);

        foreach ($variables as $key=>$variable)
            $json[$variable] = $variables_data[$key];

        $formattedData = json_encode($json);
        $handle = fopen($variables_file_path,'w+');
        fwrite($handle,$formattedData);
        fclose($handle);
    }
}
