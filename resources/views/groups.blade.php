@extends('layouts.app')

@section('content')
<?php
    $btns = [
        create_button('', __('groups_lng.synchronize'), 'btn btn-success', '', 'fa fa-retweet', '', 'onclick="synchronize_users(this.parentNode.parentNode)"'),
        create_button('', __('groups_lng.packets'), 'btn btn-info', '', 'fas fa-box-open', '', 'onclick="show_packets(this.parentNode.parentNode)"'),
        create_button('', __('groups_lng.users'), 'btn btn-primary', '', 'fas fa-user', '', 'onclick="show_users(this.parentNode.parentNode)"'),
    ];
?>
    <div class="page-header">

        <!-- START panel-->
        <div id="panelDemo9" class="panel panel-info">
            <div class="panel-heading bg-purple">
                {{__("groups_lng.groups") }}
            </div>
            <!-- Start body -->
            <div class="panel-body">
                {{ createTable($groups_cols, $groups, $btns, ['add', 'edit', 'del']) }}
            </div>
            <!-- End body -->
        </div>
        <!-- END panel-->
    </div>

    <?php
    begin_modal('modal_addEdit');
    echo Form::open(['id'=>'form_addEdit', 'action' => ['GroupController@store'], 'method'=>'POST','enctype'=>'multipart/form-data']);
        echo Form::hidden ('id', '', ['id'=>'id']);
        echo Form::hidden ('post_type', '', ['id'=>'post_type']);

        begin_row();
            create_input_group('name', __('groups_lng.name'), 'fa fa-users', 'text');
        end_row();

        begin_row();
            create_input_group('description', __('groups_lng.description'), 'fa fa-comment', 'textarea');
        end_row();
    echo Form::close();
    end_modal(['close', 'add', 'edit'], 'form_addEdit');

    echo Form::open(['id'=>'form_del', 'action' => ['GroupController@destroy', 0], 'method'=>'POST','enctype'=>'multipart/form-data']);
        echo Form::hidden('_method' ,'DELETE');
    echo Form::close();
    ?>
@endsection