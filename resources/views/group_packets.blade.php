@extends('layouts.app')

@section('content')
    <div class="page-header">

        <!-- START panel-->
        <div id="panelDemo9" class="panel panel-info">
            <div class="panel-heading bg-purple">
                <a class='link-back' href='/groups'>{{ $groupName }}</a> ->
                {{__("groups_lng.group_packets") }}
            </div>
            <!-- Start body -->
            <div class="panel-body">
                {{createTable($cols, $group_packets, [], ['edit'], $extra_columns) }}
            </div>
            <!-- End body -->
        </div>
        <!-- END panel-->
    </div>

    <?php
    begin_modal('modal_addEdit');
        echo Form::open(['id'=>'form_addEdit', 'action' => ['GroupController@store_group_packets'], 'method'=>'POST','enctype'=>'multipart/form-data']);
            echo Form::hidden ('group_id', $group_id, ['id'=>'group_id']);
            echo Form::hidden ('ids', '', ['id'=>'ids']);

            begin_row();
                create_input_group('admin_price', __('groups_lng.selling_price'), 'fa fa-money', 'number');
            end_row();
            begin_row();
                create_input_group('is_available', __('groups_lng.is_available'), 'fa fa-sitemap', 'select', $is_available_select);
            end_row();
            begin_row();
                echo create_checkbox('is_update_on_all_users', __('groups_lng.is_update_on_all_users'), '', 1);
            end_row();

        echo Form::close();
    end_modal(['close', 'add', 'edit'], 'form_addEdit');
    ?>
@endsection