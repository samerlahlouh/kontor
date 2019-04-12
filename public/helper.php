<?php
 function CallAPI($site_url, $api, $data){
    $curl_opt_url = $site_url."/$api?";

    foreach ($data as $key => $value)
        $curl_opt_url .= $key.'='.$value.'&';
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $curl_opt_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "",
        CURLOPT_HTTPHEADER => array(
            "Postman-Token: 41b96a65-4dfa-4e0d-b75c-285fe3c59d92",
            "cache-control: no-cache"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        return $response;
    }
}

function get_operators_that_have_api(){
    // You need real data here
     return [
         // 'turkcell',
         'vodafone',
         'avia'
     ];
}

function get_api_data($type, $data) // $data = ['order_id' => order_id, 'operator' => operator, 'packet_type' => packet_type, 'mobile' => mobile, 'api_id' => api_id] Chosen Columns
{
    $site_url = 'http://bayi.heteb.com/servis';
    $api_data = [];
    if($type == 'user_status_check')
        $api_data = get_user_status_check_api_data($data); // $data = ['operator' => operator]
    elseif ($type == 'send_num_for_transfer')
        $api_data = get_send_num_for_transfer_api_data($data); // $data = ['order_id' => order_id, 'operator' => operator, 'packet_type' => packet_type, 'mobile' => mobile]
    elseif ($type == 'transfer_status_check')
        $api_data = get_transfer_status_check_api_data($data); // $data = ['order_id' => order_id, 'operator' => operator]

    $api_data['site_url'] = $site_url;
    return $api_data;
}
// $api_data =  [
//                  'site_url'      => 'http://bayi.heteb.com/servis',
//                  'api'           => '....php'
//                  'params_data'   => ['bayi_kodu', 'sifre', .....]
//              ]

//--------------------------------------------------- Help functions for get_api_data function -------------------------------------------------------//
function get_user_status_check_api_data($data){
    $api = 'bakiye_kontrol.php';

    $operator = $data['operator'];
    $operator_data = get_operator_data($operator);

    $params_data['kod'] = $operator_data['kod'];
    $params_data['sifre'] = $operator_data['sifre'];

    $api_data['api'] = $api;
    $api_data['params_data'] = $params_data;

    return $api_data;
}
function get_send_num_for_transfer_api_data($data){
    $api = 'tl_servis.php';

    $operator = $data['operator'];
    $operator_data = get_operator_data($operator);

    $type = $data['packet_type'];
    $real_type_name = get_real_type_name($type);

    $params_data['bayi_kodu']   = $operator_data['kod'];
    $params_data['sifre']       = $operator_data['sifre'];
    $params_data['operator']    = $operator_data['operator'];
    $params_data['tip']         = $real_type_name;
    $params_data['kontor']      = $data['api_id'];
    $params_data['gsmno']       = $data['mobile'];
    $params_data['tekilnumara'] = '2000'.$data['order_id'];

    $api_data['api'] = $api;
    $api_data['params_data'] = $params_data;

    return $api_data;
}
function get_transfer_status_check_api_data($data){
    $api = 'tl_kontrol.php';

    $operator = $data['operator'];
    $operator_data = get_operator_data($operator);

    $params_data['bayi_kodu']   = $operator_data['kod'];
    $params_data['sifre']       = $operator_data['sifre'];
    $params_data['tekilnumara'] = '2000'.$data['order_id'];

    $api_data['api'] = $api;
    $api_data['params_data'] = $params_data;

    return $api_data;
}
function get_operator_data($operator){
    $operators_file_path = public_path() . DIRECTORY_SEPARATOR . 'variables.json';
    $data = file_get_contents ($operators_file_path);
    $json = json_decode($data, true);

    return $json['operators'][$operator];

}
function get_real_type_name($type){
    $operators_file_path = public_path() . DIRECTORY_SEPARATOR . 'variables.json';
    $data = file_get_contents ($operators_file_path);
    $json = json_decode($data, true);

    return $json['types'][$type];
}
//----------------------------------------------------------------------------------------------------------------------------------------------------//

function sendMessage($title, $message)
{
    $app_id = "c4b1a2cc-5d2e-4873-a348-a3c7b94c01b6";
    $rest_api_key = "ZTY5ODYyOTQtZGZmMi00NmEwLTg2ODYtOWU3YmNlZDkwNWE4";
    $heading = array(
        "en" => $title
    );
    $content = array(
        "en" => $message
    );
    $fields = array(
        'app_id' => $app_id,
        'included_segments' => array('All'),
        'data' => array("foo" => "bar"),
        'large_icon' =>"http://www.hurdatakip.com/resources/assets/images/icon.png",
        'contents' => $content,
        'headings' => $heading
    );

    $fields = json_encode($fields);
//        print("\nJSON sent:\n");
//        print($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic ' . $rest_api_key
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function recursive_change_key($arr, $set) { // $new_arr = recursive_change_key($old_arr, array('old_key1' => 'new_key1', 'old_key2' => 'new_key2'));
    if (is_array($arr) && is_array($set)) {
        $newArr = array();
        foreach ($arr as $k => $v) {
            $key = array_key_exists( $k, $set) ? $set[$k] : $k;
            $newArr[$key] = is_array($v) ? recursive_change_key($v, $set) : $v;
        }
        return $newArr;
    }
    return $arr;
}
?>