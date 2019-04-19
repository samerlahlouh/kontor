@extends('layouts.app')

@section('content')
    <?php
    $btns = [
        create_button('', '', 'btn activator'),
        create_button('', '', 'btn is_checking_free')
    ];
    ?>

    <div class="page-header">

        <!-- START panel-->
        <div id="panelDemo9" class="panel panel-info">
            <div class="panel-heading bg-purple">
                {{__("users_lng.users") }}
            </div>
            <!-- Start body -->
            <div class="panel-body">
                {{ createTable($cols, $users, $btns) }}
            </div>
            <!-- End body -->
        </div>
        <!-- END panel-->
    </div>
@endsection