@extends('layouts.app')

@section('content')
    <div class="page-header">

        <!-- START panel-->
        <div id="panelDemo9" class="panel panel-info">
            <div class="panel-heading bg-purple">
                <a class='link-back' href='/groups'>{{ $groupName }}</a> ->
                {{__("groups_lng.users") }}
            </div>
            <!-- Start body -->
            <div class="panel-body">
                {{ createTable($cols, $users, [], ['add', 'del']) }}
            </div>
            <!-- End body -->
        </div>
        <!-- END panel-->
    </div>

    <?php
    begin_modal('modal_addEdit');
        echo Form::open(['id'=>'form_addEdit', 'action' => ['GroupController@store_group_user'], 'method'=>'POST','enctype'=>'multipart/form-data']);
            echo Form::hidden ('group_id', $group_id, ['id'=>'id']);

            begin_row();
                create_input_group('user_id', __('groups_lng.user'), 'fa fa-user', 'select', $select_users);
            end_row();
        echo Form::close();
    end_modal(['close', 'add'], 'form_addEdit');

    echo Form::open(['id'=>'form_del', 'action' => ['GroupController@destroy_group_user', 0], 'method'=>'POST','enctype'=>'multipart/form-data']);
        echo Form::hidden('_method' ,'DELETE');
    echo Form::close();
    ?>
@endsection