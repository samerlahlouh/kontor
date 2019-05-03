@extends('layouts.app')

@section('content')
    <?php
    begin_card('fa fa-cog', __('settings_lng.app_settings') );
        echo Form::open(['id'=>'form_edit', 'action' => 'SettingsController@update', 'method'=>'POST','enctype'=>'multipart/form-data']) ;
            begin_incubated_child_card();
                begin_child_card('card_info', __('settings_lng.info'), 'show');
                    begin_row();
                        create_input_group('app_title', __('settings_lng.app_title'), 'fa fa-ravelry', 'text', [], [], $app_title);
                    end_row();
                end_child_card();

                begin_child_card('card_send_notification', __('settings_lng.send_notification_info'), 'show');
                    begin_row();
                        create_input_group('send_notification_app_id', __('settings_lng.app_id'), 'fa fa-hashtag', 'text', [], [], $send_notification_app_id);
                    next_col();
                        create_input_group('send_notification_rest_api_key', __('settings_lng.rest_api_key'), 'fa fa-hashtag', 'text', [], [], $send_notification_rest_api_key);
                    end_row();
                end_child_card();
            end_incubated_child_card();
        echo Form::close();
    end_card('', ['update'], 'form_edit');
    ?>
@endsection
