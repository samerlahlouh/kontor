@extends('layouts.app')

@section('content')
    <?php
    begin_card('fa fa-user', __('change_user_password_lng.user_password') );
    echo Form::open(['id'=>'form_edit', 'action' => 'UserController@update_user_password', 'method'=>'POST','enctype'=>'multipart/form-data']) ;

    echo Form::hidden ('user_id', $user_id, ['id'=>'user_id']);

    begin_incubated_child_card();
    begin_child_card('card_account_info', __('change_user_password_lng.user_password'), 'show');

    begin_row();
    create_input_group('new_password', __('change_user_password_lng.new_password'), 'fa fa-hashtag', 'password');
    next_col();
    create_input_group('confirm_password', __('change_user_password_lng.confirm_password'), 'fa fa-hashtag', 'password');
    end_row();

    end_child_card();
    end_incubated_child_card();
    echo Form::close();
    end_card('', ['update'], 'form_edit');
    ?>
@endsection