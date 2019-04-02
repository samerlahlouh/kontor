<?php
$checking_orders_btns = [
    create_button('', __('home_lng.send_result'), 'btn btn-primary', '', 'fa fa-rocket', '', 'onclick="send_result(this.parentNode.parentNode)"'),
    create_button('', __('home_lng.reject'), 'btn btn-danger', '', 'fas fa-times', '', 'onclick="change_status(this.parentNode.parentNode, \'rejected\')"'),
];
?>

{{ createTable($checking_orders_cols, $checking_orders, $checking_orders_btns) }}