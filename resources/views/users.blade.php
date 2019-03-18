@extends('layouts.app')

@section('content')

<?php
$btns = [
    create_button('', '', 'btn activator'),
    // create_button('',  __('users_lng.activate'), 'btn btn-success', '', 'fa fa-user-plus', '', 'onclick="activate_user(this.parentNode.parentNode)"')
];
?>

<div class="page-header">

 <!-- START panel-->
 <div id="panelDemo9" class="panel panel-info">
    <div class="panel-heading bg-purple">
         {{__("users_lng.users") }}
    </div>
        <!-- Start body -->
        <div class="panel-body">
            {{createTable($cols, $users, $btns, ['add', 'del']) }}
        </div>
        <!-- End body -->
    </div>
    <!-- END panel-->
</div>

<?php 
begin_modal('modal_addEdit');
    echo Form::open(['id'=>'form_addEdit', 'action' => ['UserController@store'], 'method'=>'POST','enctype'=>'multipart/form-data']);
        echo Form::hidden ('id', '', ['id'=>'id']);

        begin_row();
            create_input_group('name', __('users_lng.name'), 'fa fa-user', 'text');
        end_row();
        begin_row();
            create_input_group('email', __('users_lng.email'), 'fa fa-envelope', 'email');
        end_row();
        begin_row();
            create_input_group('mobile', __('users_lng.mobile'), 'fas fa-mobile', 'number');
        end_row();
        begin_row();
            create_input_group('user_name', __('users_lng.user_name'), 'fa fa-user', 'text');
        end_row();
        if(Auth::user()->type == 'admin'){
            begin_row();
                create_input_group('type', __('users_lng.type'), 'fa fa-sitemap', 'select', $types);
            end_row();
        }
        begin_row();
            create_input_group('password', __('users_lng.password'), 'fas fa-key', 'password');
        end_row();
        begin_row();
            create_input_group('confirm_password', __('users_lng.confirm_password'), 'fas fa-key', 'password');
        end_row();
    
    echo Form::close();
end_modal(['close', 'add', 'edit'], 'form_addEdit');

echo Form::open(['id'=>'form_del', 'action' => ['UserController@destroy', 0], 'method'=>'POST','enctype'=>'multipart/form-data']);
    echo Form::hidden('_method' ,'DELETE');
echo Form::close();?>
@endsection