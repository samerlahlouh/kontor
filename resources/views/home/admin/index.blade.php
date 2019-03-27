@extends('layouts.app')

@section('content')
<div id="content_div">
<?php
    begin_incubated_child_card();
        begin_child_card('card_checking_orders', __('home_lng.checking_orders'), 'show');
        
        ?>
        <?php end_child_card();

        begin_child_card('card_checking_transfers', __('home_lng.checking_transfers'), 'show');
        ?>
        <?php end_child_card();

        begin_child_card('card_chargings', __('home_lng.chargings'), 'show');
        ?>
        <?php end_child_card();
    end_incubated_child_card();
    
?>
</div>
@endsection