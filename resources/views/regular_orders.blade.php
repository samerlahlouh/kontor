@extends('layouts.app')

@section('content')
<div class="page-header">

 <!-- START panel-->
 <div id="panelDemo9" class="panel panel-info">
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