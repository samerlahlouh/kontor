<?php
$chargings_btns = [
    create_button('', __('home_lng.reject'), 'btn btn-danger', '', 'fas fa-times', '', 'onclick="change_charging_status(this.parentNode.parentNode, \'rejected\')"'),
    create_button('', __('home_lng.accept'), 'btn btn-success', '', 'fa fa-check', '', 'onclick="change_charging_status(this.parentNode.parentNode, \'accepted\')"'),
];
?>

{{createTable($chargings_cols, $chargings, $chargings_btns) }}