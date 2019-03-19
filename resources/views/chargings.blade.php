@extends('layouts.app')

@section('content')
<div class="page-header">

 <!-- START panel-->
 <div id="panelDemo9" class="panel panel-info">
    <div class="panel-heading bg-purple">
         {{__("chargings_lng.chargings") }}
    </div>
        <!-- Start body -->
        <div class="panel-body">
            {{createTable($cols, $chargings, [], ['add', 'edit', 'del']) }}
        </div>
        <!-- End body -->
    </div>
    <!-- END panel-->
</div>

<?php 
begin_modal('modal_addEdit');
    echo Form::open(['id'=>'form_addEdit', 'action' => ['ChargingController@store'], 'method'=>'POST','enctype'=>'multipart/form-data']);
        echo Form::hidden ('id', '', ['id'=>'id']);

        begin_row();
            create_input_group('user_id', __('chargings_lng.user'), 'fa fa-user', 'select', $select_users);
        end_row();
        begin_row();
            create_input_group('type', __('chargings_lng.type'), 'fa fa-sitemap', 'select', $select_types);
        end_row();
        begin_row();
            create_input_group('status', __('chargings_lng.status'), 'fa fa-info', 'select', $select_statuses);
        end_row();
        begin_row();
            create_input_group('amount', __('chargings_lng.amount'), 'fa fa-money', 'number');
        end_row();
        begin_row();
            create_input_group('request_date', __('chargings_lng.request_date'), 'fa fa-calendar-o', 'date');
        end_row();
        begin_row();
            create_input_group('response_date', __('chargings_lng.response_date'), 'fa fa-calendar-o', 'date');
        end_row();
        begin_row();
            create_input_group('notes', __('chargings_lng.notes'), 'fa fa-commenting', 'textarea');
        end_row();
    
    echo Form::close();
end_modal(['close', 'add', 'edit'], 'form_addEdit');

echo Form::open(['id'=>'form_del', 'action' => ['ChargingController@destroy', 0], 'method'=>'POST','enctype'=>'multipart/form-data']);
    echo Form::hidden('_method' ,'DELETE');
echo Form::close();
?>
@endsection