@extends('layouts.app')

@section('content')
<div class="page-header">

 <!-- START panel-->
 <div id="panelDemo9" class="panel panel-info">
    <div class="panel-heading bg-purple">
         {{__("operators_lng.operators") }}
    </div>
        <!-- Start body -->
        <div class="panel-body">
                {{createTable($operators_cols, $operators, [], ['add', 'edit', 'del']) }}
        </div>
        <!-- End body -->
    </div>
    <!-- END panel-->
</div>

<?php 
begin_modal('modal_addEdit');
    echo Form::open(['id'=>'form_addEdit', 'action' => ['OperatorController@store'], 'method'=>'POST','enctype'=>'multipart/form-data']);
        echo Form::hidden ('post_type', '', ['id'=>'post_type']);
        echo Form::hidden ('old_operator', '', ['id'=>'old_operator']);

        begin_row();
            create_input_group('operator', __('operators_lng.operator'), 'fa fa-building', 'text');
        end_row();
    echo Form::close();
end_modal(['close', 'add', 'edit'], 'form_addEdit');

echo Form::open(['id'=>'form_del', 'action' => ['OperatorController@destroy', 0], 'method'=>'POST','enctype'=>'multipart/form-data']);
    echo Form::hidden('_method' ,'DELETE');
echo Form::close();
?>
@endsection