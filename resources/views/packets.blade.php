@extends('layouts.app')

@section('content')

<?php
$btns = [
    create_button('', __('packets_lng.users'), 'btn btn-primary', '', 'fas fa-user', '', 'onclick="show_packet_users(this.parentNode.parentNode)"')
];

$controlBtns = [];
if(Auth::user()->type == 'admin')
    $controlBtns = ['add', 'edit', 'del'];
?>

<div class="page-header">

 <!-- START panel-->
 <div id="panelDemo9" class="panel panel-info">
    <div class="panel-heading bg-purple">
         {{__("packets_lng.packets") }}
    </div>
        <!-- Start body -->
        <div class="panel-body">
            {{createTable($cols, $packets, $btns, $controlBtns) }}
        </div>
        <!-- End body -->
    </div>
    <!-- END panel-->
</div>




<?php 
begin_modal('modal_addEdit');
    echo Form::open(['id'=>'form_addEdit', 'action' => ['PacketController@store'], 'method'=>'POST','enctype'=>'multipart/form-data']);
        echo Form::hidden ('id', '', ['id'=>'id']);

        begin_row();
            create_input_group('name', __('packets_lng.name'), 'fa fa-user', 'text');
        end_row();
        begin_row();
            create_input_group('operator', __('packets_lng.operator'), 'fa fa-user', 'select', $operators);
        end_row();
        begin_row();
            create_input_group('sms', __('packets_lng.sms'), 'fa fa-envelope', 'number');
        end_row();
        begin_row();
            create_input_group('minutes', __('packets_lng.minutes'), 'fas fa-mobile', 'number');
        end_row();
        begin_row();
            create_input_group('internet', __('packets_lng.internet'), 'fa fa-globe', 'number');
        end_row();
        begin_row();
            create_input_group('type', __('packets_lng.type'), 'fa fa-sitemap', 'select', $types);
        end_row();
        begin_row();
            create_input_group('price', __('packets_lng.price'), 'fa fa-money', 'number');
        end_row();
        begin_row();
            create_input_group('is_global', __('packets_lng.is_global'), 'fa fa-users', 'select', $is_global);
        end_row();
        begin_row();
            create_input_group('is_teens', __('packets_lng.is_teens'), 'fa fa-male', 'select', $is_teens);
        end_row();
    
    echo Form::close();
end_modal(['close', 'add', 'edit'], 'form_addEdit');

echo Form::open(['id'=>'form_del', 'action' => ['PacketController@destroy', 0], 'method'=>'POST','enctype'=>'multipart/form-data']);
    echo Form::hidden('_method' ,'DELETE');
echo Form::close();
?>
@endsection