@extends('layouts.app')

@section('content')
<?php
begin_card('fa fa-user', __('user_settings_lng.user_settings') );
echo Form::open(['id'=>'form_edit', 'action' => 'UserController@update', 'method'=>'POST','enctype'=>'multipart/form-data']) ;
    begin_incubated_child_card();
        begin_child_card('card_account_info', __('user_settings_lng.account_info'), 'show');
            begin_row();
                create_input_group('name', __('user_settings_lng.name'), 'fa fa-user', 'text', [], [], Auth::user()->name);
            end_row();
                
            begin_row();
                create_input_group('email', __('user_settings_lng.email'), 'fa fa-envelope', 'text', [], [], Auth::user()->email);
            next_col();
                create_input_group('user_name', __('user_settings_lng.user_name'), 'fa fa-user', 'text', [], [], Auth::user()->user_name);
            end_row();

            begin_row();
                create_input_group('old_password', __('user_settings_lng.old_password'), 'fa fa-envelope', 'password');
           end_row();

           begin_row();
                create_input_group('new_password', __('user_settings_lng.new_password'), 'fa fa-envelope', 'password');
            next_col();
                create_input_group('confirm_password', __('user_settings_lng.confirm_password'), 'fa fa-user', 'password');
            end_row();
        end_child_card();
    end_incubated_child_card();
echo Form::close();
end_card('', ['update'], 'form_edit');
?>
@endsection