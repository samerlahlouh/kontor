<?php

namespace Educators\Http\Controllers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Config;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function __construct()
    {
        $url = url()->current();
        $create = substr($url, -7);
        $edit = substr($url, -5);
        if ($create == '/create' || $edit == '/edit')
            $this->middleware('auth');
    }

    public function change_lang(Request $request){
        $lang = $request->lang;
        if (in_array($lang, \Config::get('app.locales'))) {
            Session::put('locale', $lang);
        }
        return redirect()->back();
    }

    public static function getEnumValues($table, $column) {
        $type = DB::select(DB::raw("SHOW COLUMNS FROM $table WHERE Field = '{$column}'"))[0]->Type ;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $enum = array();
        foreach( explode(',', $matches[1]) as $value )
        {
          $v = trim( $value, "'" );
          $enum = array_add($enum, $v, $v);
        }
        return $enum;
      }
}
