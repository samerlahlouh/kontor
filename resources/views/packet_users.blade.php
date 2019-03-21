@extends('layouts.app')

@section('content')
<div class="page-header">

 <!-- START panel-->
 <div id="panelDemo9" class="panel panel-info">
    <div class="panel-heading bg-purple">
        <a class='link-back' href='/packets'>{{ $packet_name }}</a> ->
         {{__("packets_lng.packet_users") }}
    </div>
        <!-- Start body -->
        <div class="panel-body">
                {{createTable($cols, $packet_users, [], ['edit'], $extra_columns) }}
        </div>
        <!-- End body -->
    </div>
    <!-- END panel-->
</div>

<?php 
begin_modal('modal_addEdit');
    echo Form::open(['id'=>'form_addEdit', 'action' => ['PacketController@store_packet_users'], 'method'=>'POST','enctype'=>'multipart/form-data']);
        echo Form::hidden ('ids', '', ['id'=>'ids']);

        begin_row();
            create_input_group('admin_price', __('packets_lng.admin_price'), 'fa fa-money', 'number');
        end_row();
       begin_row();
            create_input_group('is_available', __('packets_lng.is_available'), 'fa fa-sitemap', 'select', $is_available_select);
        end_row();
    
    echo Form::close();
end_modal(['close', 'add', 'edit'], 'form_addEdit');
?>
@endsection