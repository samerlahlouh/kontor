@extends('layouts.app')

@section('content')
<div class="page-header">

 <!-- START panel-->
 <div id="panelDemo9" class="panel panel-info">
    <div class="panel-heading bg-purple">
         {{__("packets_types_lng.types") }}
    </div>
        <!-- Start body -->
        <div class="panel-body">
                {{createTable($packets_types_cols, $packets_types, [], ['add', 'edit', 'del']) }}
        </div>
        <!-- End body -->
    </div>
    <!-- END panel-->
</div>

<?php 
begin_modal('modal_addEdit');
    echo Form::open(['id'=>'form_addEdit', 'action' => ['PacketTypeController@store'], 'method'=>'POST','enctype'=>'multipart/form-data']);
        echo Form::hidden ('post_type', '', ['id'=>'post_type']);
        echo Form::hidden ('old_type', '', ['id'=>'old_type']);

        begin_row();
            create_input_group('type', __('packets_types_lng.type'), 'fa fa-sitemap', 'text');
        end_row();

        begin_row();
            create_input_group('real_type_name', __('packets_types_lng.real_type_name'), 'fa fa-sitemap', 'text');
        end_row();
    echo Form::close();
end_modal(['close', 'add', 'edit'], 'form_addEdit');

echo Form::open(['id'=>'form_del', 'action' => ['PacketTypeController@destroy', 0], 'method'=>'POST','enctype'=>'multipart/form-data']);
    echo Form::hidden('_method' ,'DELETE');
echo Form::close();
?>
@endsection