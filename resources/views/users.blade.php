@extends('layouts.app')

@section('content')
<?php
$btns = [
    create_button('', __('users_lng.synchronize'), 'btn btn-success', '', 'fa fa-retweet', '', 'onclick="synchronize_user(this.parentNode.parentNode)"'),
//    create_button('', '', 'btn activator'),
    create_button('', __('users_lng.password'), 'btn btn-info', '', 'fa fa-lock', '', 'onclick="change_password(this.parentNode.parentNode)"'),
    create_button('', __('users_lng.packets'), 'btn btn-primary', '', 'fas fa-box-open', '', 'onclick="show_packets(this.parentNode.parentNode)"'),
//    create_button('', '', 'btn is_checking_free')
];


$extra_columns = [
    // [
    //     'type'=>'checkbox',
    //     'title'=>'title',
    //     'text'=>'text',
    //     'class'=>'class'
    // ]
];

$extra_columns_values = [
    // [
    //     '1',
    //     '1',
    //     '0'
    // ]
];
// __('users_lng.packets')
?>

<div class="page-header">

 <!-- START panel-->
 <div id="panelDemo9" class="panel panel-info">
    <div class="panel-heading bg-purple">
         {{__("users_lng.users") }}
    </div>
        <!-- Start body -->
        <div class="panel-body">
                {{ createTable($cols, $users, $btns, ['add', 'del'], $extra_columns, $extra_columns_values) }}
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
            begin_row();
            create_input_group('group_id', __('users_lng.group'), 'fa fa-users', 'select', $select_groups);
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