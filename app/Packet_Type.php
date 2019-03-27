<?php

namespace Educators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Packet_Type extends Model
{
    public function add_value_to_types_field($types, $value){
        $types_str = '';
        foreach ($types as $type)
            $types_str .= ",'$type'";
        $types_str .= ",'$value'";
        $types_str = substr($types_str, 1);
        DB::statement("ALTER TABLE `packets` CHANGE `type` `type` ENUM($types_str);");
    }

    public function update_types_field($types){
        $types_str = '';
        foreach ($types as $type)
            $types_str .= ",'$type'";
        $types_str = substr($types_str, 1);
        DB::statement("ALTER TABLE `packets` CHANGE `type` `type` ENUM($types_str);");
    }
}
