<?php
$checking_orders_btns = [
    create_button('', '', 'btn btn-danger', '', 'fas fa-times', '', 'onclick="cancel_order(this.parentNode.parentNode, 2)"'),
    create_button('', __('home_lng.transfer'), 'btn btn-success transfer', '', 'fa fa-rocket', '', 'onclick="make_packet_in_transfer_status($(this).parent().parent())"')
];
?>

<div class="page-header">
<!-- START panel-->
<div id="panel_checking_orders" class="panel panel-info">
    <div class="panel-heading bg-purple">
         {{__('home_lng.checking_orders') }}
        <a class="{{$pull}} play-refresh">
            {{__('home_lng.refresh_is_working') }}
            <i class="fa fa-pause-circle refresh-btn"></i>
        </a>
        <a class="{{$pull}} stop-refresh">
            {{__('home_lng.refresh_is_stopped') }}
            <i class="fa fa-play-circle refresh-btn"></i>
        </a>
    </div>
        <!-- Start body -->
        <div class="panel-body">
                {{createTable($checking_orders_cols, $checking_orders, $checking_orders_btns, [], $checking_orders_extra_cols) }}
        </div>
        <!-- End body -->
    </div>
    <!-- END panel-->
</div>
