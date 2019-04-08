@extends('layouts.app')

@section('content')

<?php
$pull = $lang == 'en'? 'pull-right':'pull-left';
$btns = [
    create_button('', __('home_lng.check_number'), "btn btn-primary $pull", 'margin-left:2px;margin-right:2px;', 'fa fa-search', '', 'onclick="check_number()"'),
    create_button('btn_transfer', __('home_lng.transfer'), "btn btn-success $pull", 'margin-left:2px;margin-right:2px;', 'fa fa-rocket', '', 'onclick="add_click()"')
];

begin_card('fa fa-rocket', __('home_lng.number_processes') );
echo Form::open(['id'=>'form_number_processes', 'action' => 'UserController@update_own_account', 'method'=>'POST','enctype'=>'multipart/form-data']) ;
    begin_incubated_child_card();
        begin_row();
            begin_row();
                create_input_group('number', __('home_lng.number'), 'fas fa-mobile', 'number', [], ['oninput'=>"maxLengthCheck(this)", 'maxlength'=> '10']);
            end_row();
            begin_row();
                create_input_group('selected_operator', __('home_lng.operator'), 'fa fa-building', 'select', $select_operators, [], 'turkcell');
            end_row();
            begin_row();
                create_input_group('customer_name', __('home_lng.customer_name'), 'fa fa-user', 'text');
            end_row();    
        next_col();
            create_input_group('message', __('home_lng.message'), 'fa fa-comment-o', 'textarea');
        end_row();
    end_incubated_child_card();
echo Form::close();
end_card('', [], 'form_number_processes', $btns);
?>

<?php 
begin_modal('modal_transfer');
    echo Form::open(['id'=>'form_transfer', 'action' => ['HomeController@transfer_packet'], 'method'=>'POST','enctype'=>'multipart/form-data']);
        echo Form::hidden ('mobile', '', ['id'=>'mobile']);
        echo Form::hidden ('customer', '', ['id'=>'customer']);
        echo Form::hidden ('operator', '', ['id'=>'operator']);

        begin_row();
            create_input_group('type', __('home_lng.type'), 'fa fa-sitemap', 'select', $select_types);
        end_row();
        begin_row();
            create_input_group('packet', __('home_lng.packet'), 'fas fa-box-open', 'select', []);
        end_row();
    echo Form::close();
end_modal(['close', 'add'], 'form_transfer');
?>

@include('home.regular.checking_orders')

@include('home.regular.checked_orders')


@endsection