@extends('layouts.app')

@section('content')
<?php
if ($is_parent){
    $pull = $lang == 'ar'? 'pull-left':'pull-right';
    $btns = [
            create_button('btn_search', __('regular_orders_lng.search'), "btn btn-success $pull", 'margin-left:2px;margin-right:2px;', 'fa fa-search', '', 'onclick="filter_orders_table()"')
    ];

    begin_card('fa fa-tasks', __('regular_orders_lng.filter') );
        begin_incubated_child_card();
            begin_row();
                create_input_group('from_date', __('regular_orders_lng.from_date'), 'fa fa-calendar-o', 'date');
            next_col();
                create_input_group('to_date', __('regular_orders_lng.to_date'), 'fa fa-calendar-o', 'date');
            end_row();
        end_incubated_child_card();
    end_card('', [], 'form_number_processes', $btns);
}
?>

<div class="page-header">

 <!-- START panel-->
 <div id="orders_panel" class="panel panel-info">
    <div class="panel-heading bg-purple">
         {{__("regular_orders_lng.orders") }}
    </div>
        <!-- Start body -->
        <div class="panel-body">
                {{createTable($orders_cols, $orders) }}
        </div>
        <!-- End body -->
    </div>
    <!-- END panel-->
</div>
@endsection