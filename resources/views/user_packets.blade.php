@extends('layouts.app')

@section('content')
<div class="page-header">

 <!-- START panel-->
 <div id="panelDemo9" class="panel panel-info">
    <div class="panel-heading bg-purple">
         {{__("users_lng.user_packets") }}
    </div>
        <!-- Start body -->
        <div class="panel-body">
                {{createTable($cols, $user_packets, [], ['edit'], $extra_columns) }}
        </div>
        <!-- End body -->
    </div>
    <!-- END panel-->
</div>

<?php 
begin_modal('modal_addEdit');
    echo Form::open(['id'=>'form_addEdit', 'action' => ['UserController@store_user_packets'], 'method'=>'POST','enctype'=>'multipart/form-data']);
        echo Form::hidden ('ids', '', ['id'=>'ids']);

        begin_row();
            create_input_group('admin_price', __('users_lng.admin_price'), 'fa fa-money', 'number');
        end_row();
       begin_row();
            create_input_group('is_available', __('users_lng.is_available'), 'fa fa-sitemap', 'select', $is_available_select);
        end_row();
    
    echo Form::close();
end_modal(['close', 'add', 'edit'], 'form_addEdit');

echo Form::open(['id'=>'form_del', 'action' => ['PacketController@destroy', 0], 'method'=>'POST','enctype'=>'multipart/form-data']);
    echo Form::hidden('_method' ,'DELETE');
echo Form::close();
?>
@endsection