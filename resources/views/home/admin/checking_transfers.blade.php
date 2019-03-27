<?php
$checking_transfers_btns = [
    create_button('', __('home_lng.reject'), 'btn btn-danger', '', 'fas fa-times', '', 'onclick="change_status(this.parentNode.parentNode, \'rejected\')"'),
    create_button('', __('home_lng.accept'), 'btn btn-primary accept', '', 'fa fa-check', '', 'onclick="change_status(this.parentNode.parentNode, \'in_progress\')"'),
    create_button('', __('home_lng.transfer_done'), 'btn btn-success', '', 'fa fa-check-square-o', '', 'onclick="change_status(this.parentNode.parentNode, \'completed\')"')
];
?>

{{createTable($checking_transfers_cols, $checking_transfers, $checking_transfers_btns) }}