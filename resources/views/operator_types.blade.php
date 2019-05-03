@extends('layouts.app')

@section('content')
    <div class="page-header">

        <!-- START panel-->
        <div id="panelDemo9" class="panel panel-info">
            <div class="panel-heading bg-purple">
                <a class='link-back' href='/operators'>{{ $operator }}</a> ->
                {{__("operators_lng.types") }}
            </div>
            <!-- Start body -->
            <div class="panel-body">
                {{createTable($types_cols, $types, [], ['add', 'del']) }}
            </div>
            <!-- End body -->
        </div>
        <!-- END panel-->
    </div>

    <?php
    begin_modal('modal_addEdit');
        echo Form::open(['id'=>'form_addEdit', 'action' => ['OperatorController@store_operator_type'], 'method'=>'POST','enctype'=>'multipart/form-data']);
            echo Form::hidden ('post_type', '', ['id'=>'post_type']);
            echo Form::hidden ('old_type', '', ['id'=>'old_type']);
            echo Form::hidden ('operator', $operator, ['id'=>'operator']);

            begin_row();
                create_input_group('type', __('operators_lng.type'), 'fa fa-sitemap', 'select', $available_types);
            end_row();
        echo Form::close();
    end_modal(['close', 'add', 'edit'], 'form_addEdit');

    echo Form::open(['id'=>'form_del', 'action' => ['OperatorController@destroy_operator_type', 0], 'method'=>'POST','enctype'=>'multipart/form-data']);
        echo Form::hidden('_method' ,'DELETE');
    echo Form::close();
    ?>


    <input type="hidden" id="operator" value="{{ $operator }}">
@endsection
