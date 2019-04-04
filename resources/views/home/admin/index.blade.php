@extends('layouts.app')

@section('content')
<div id="content_div">
<?php
    begin_incubated_child_card();
        begin_child_card('card_checking_orders', __('home_lng.checking_orders'), 'show');?>
            @include('home.admin.checking_orders')
        <?php end_child_card();

        if(Auth::user()->type == 'admin'){
            begin_child_card('card_checking_transfers', __('home_lng.checking_transfers'), 'show');?>
                @include('home.admin.checking_transfers')
            <?php end_child_card();
        }

            begin_child_card('card_chargings', __('home_lng.chargings'), 'show');?>
                @include('home.admin.chargings')
            <?php end_child_card();
    end_incubated_child_card();

    if(Auth::user()->type == 'admin'){
        begin_modal('modal_send_result');
            echo Form::open(['id'=>'form_send_result', 'action' => ['HomeController@send_result_to_user'], 'method'=>'POST','enctype'=>'multipart/form-data']);
                echo Form::hidden ('id', '', ['id'=>'id']);

                begin_row();
                    create_input_group('customer_name', __('home_lng.customer_name'), 'fa fa-user', 'text', [], ['disabled']);
                end_row();
                begin_row();
                    create_input_group('mobile', __('home_lng.mobile'), 'fa fa-hashtag', 'text', [], ['disabled']);
                end_row();

                begin_row();
                    echo create_button('', __('home_lng.select_all'), 'btn btn-primary', '', 'fa fa-check-square', '', 'onclick="select_all()"');
                next_col();
                    echo create_button('', __('home_lng.unselect_all'), 'btn btn-primary', '', 'fa fa-square-o', '', 'onclick="unselect_all()"');
                next_col();
                    echo create_button('', __('home_lng.teen_packet'), 'btn btn-primary', '', 'fa fa-square', '', 'onclick="select_teen_packet()"');
                end_row();

                begin_row();

                foreach ($packets as $key => $packet) {?>
                    <div id="{{$key}}_packets" class="operator_packets" style="display: none;">
                        <?php create_checkbox_group('packet_ids', $packet['names'], $packet['ids'], 'packet', [], '', $packet['is_teens']);?>
                    </div>
                <?php
                }
                end_row();

                echo Form::close();
        end_modal(['close', 'add'], 'form_send_result');
    }
?>
</div>
@endsection