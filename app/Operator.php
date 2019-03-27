<?php

namespace Educators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Operator extends Model
{
    public function add_value_to_operators_field($operators, $value){
        $operators_str = '';
        foreach ($operators as $operator)
            $operators_str .= ",'$operator'";
        $operators_str .= ",'$value'";
        $operators_str = substr($operators_str, 1);
        DB::statement("ALTER TABLE `packets` CHANGE `operator` `operator` ENUM($operators_str) default 'turkcell' ;");
    }

    public function update_operators_field($operators){
        $operators_str = '';
        foreach ($operators as $operator)
            $operators_str .= ",'$operator'";
        $operators_str = substr($operators_str, 1);
        DB::statement("ALTER TABLE `packets` CHANGE `operator` `operator` ENUM($operators_str);");
    }
}
