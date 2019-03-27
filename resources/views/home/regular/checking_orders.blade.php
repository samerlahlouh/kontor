<?php
$checking_orders_btns = [
    create_button('', '', 'btn btn-danger', '', 'fas fa-times', '', 'onclick="cancel_order(this.parentNode.parentNode, 2)"'),
    create_button('', __('home_lng.transfer'), 'btn btn-success transfer', '', 'fa fa-rocket', '', 'onclick="make_packet_in_transfer_status($(this).parent().parent())"')
];
?>

<div class="page-header">
<!-- START panel-->
<div id="panelDemo9" class="panel panel-info">
    <div class="panel-heading bg-purple">
         {{__('home_lng.checking_orders') }}
    </div>
        <!-- Start body -->
        <div class="panel-body">
                {{createTable($checking_orders_cols, $checking_orders, $checking_orders_btns, [], $checking_orders_extra_cols) }}
        </div>
        <!-- End body -->
    </div>
    <!-- END panel-->
</div>