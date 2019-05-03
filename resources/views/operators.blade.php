@extends('layouts.app')

@section('content')
<?php
    $btns = [
        create_button('', __('operators_lng.types'), 'btn btn-primary', '', 'fa fa-sitemap', '', 'onclick="show_operator_types(this.parentNode.parentNode)"'),
    ];
?>
<div class="page-header">

 <!-- START panel-->
 <div id="panelDemo9" class="panel panel-info">
    <div class="panel-heading bg-purple">
         {{__("operators_lng.operators") }}
    </div>
        <!-- Start body -->
        <div class="panel-body">
                {{createTable($operators_cols, $operators, $btns, ['add', 'edit']) }}
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
        begin_row();
            create_input_group('api_user_name', __('operators_lng.api_user_name'), 'fa fa-user', 'text');
        end_row();
        begin_row();
            create_input_group('api_password', __('operators_lng.api_password'), 'fa fa-hashtag', 'text');
        end_row();
        begin_row();
            create_input_group('api_operator', __('operators_lng.api_operator'), 'fa fa-building', 'text');
        end_row();
        begin_row();
            create_input_group('site_url', __('operators_lng.site_url'), 'fa fa-globe', 'text');
        end_row();
        begin_row();
            echo create_checkbox('is_api', __('operators_lng.is_api'), '', 0);
        end_row();
    echo Form::close();
end_modal(['close', 'add', 'edit'], 'form_addEdit');

echo Form::open(['id'=>'form_del', 'action' => ['OperatorController@destroy', 0], 'method'=>'POST','enctype'=>'multipart/form-data']);
    echo Form::hidden('_method' ,'DELETE');
echo Form::close();
?>
@endsection
