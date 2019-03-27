@extends('layouts.app')

@section('content')
<?php
$btns = [
    create_button('', __('chargings_lng.cancel'), 'btn btn-danger cancel-btn', '', 'fas fa-times', '', 'onclick="charging_cancel(this.parentNode.parentNode)"')
];
?>

<div class="page-header">

 <!-- START panel-->
 <div id="panelDemo9" class="panel panel-info">
    <div class="panel-heading bg-purple">
         {{__("chargings_lng.chargings") }}
    </div>
        <!-- Start body -->
        <div class="panel-body">
            {{createTable($cols, $regular_chargings, $btns, ['add']) }}
        </div>
        <!-- End body -->
    </div>
    <!-- END panel-->
</div>

<?php 
begin_modal('modal_addEdit');
    echo Form::open(['id'=>'form_addEdit', 'action' => ['ChargingController@store_regular_charing'], 'method'=>'POST','enctype'=>'multipart/form-data']);
        echo Form::hidden ('id', '', ['id'=>'id']);

        begin_row();
            create_input_group('type', __('chargings_lng.type'), 'fa fa-sitemap', 'select', $select_types);
        end_row();
        begin_row();
            create_input_group('amount', __('chargings_lng.amount'), 'fa fa-money', 'number');
        end_row();
        begin_row();
            create_input_group('notes', __('chargings_lng.notes'), 'fas fa-commenting', 'textarea');
        end_row();
    
    echo Form::close();
end_modal(['close', 'add', 'edit'], 'form_addEdit');

echo Form::open(['id'=>'form_del', 'action' => ['PacketController@destroy', 0], 'method'=>'POST','enctype'=>'multipart/form-data']);
    echo Form::hidden('_method' ,'DELETE');
echo Form::close();
?>
@endsection