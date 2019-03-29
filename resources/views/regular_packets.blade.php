@extends('layouts.app')

@section('content')
<?php
$btns = [
    create_button('', __('regular_packets_lng.notes'), 'btn btn-success', '', 'fa fa-info-circle', '', 'onclick="show_notes(this.parentNode.parentNode)"')
];
?>

<div class="page-header">

 <!-- START panel-->
 <div id="panelDemo9" class="panel panel-info">
    <div class="panel-heading bg-purple">
         {{__("regular_packets_lng.packets") }}
    </div>
        <!-- Start body -->
        <div class="panel-body">
                {{createTable($cols, $regular_packets, $btns, ['edit'], $extra_columns) }}
        </div>
        <!-- End body -->
    </div>
    <!-- END panel-->
</div>

<?php 
begin_modal('modal_addEdit');
    echo Form::open(['id'=>'form_addEdit', 'action' => ['PacketController@store_regular_packets'], 'method'=>'POST','enctype'=>'multipart/form-data']);
        echo Form::hidden ('ids', '', ['id'=>'ids']);

        begin_row();
            create_input_group('user_price', __('regular_packets_lng.selling_price'), 'fa fa-money', 'number');
        end_row();
    
    echo Form::close();
end_modal(['close', 'add', 'edit'], 'form_addEdit');
?>
@endsection