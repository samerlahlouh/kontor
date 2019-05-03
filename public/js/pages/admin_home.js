$(document).ready(function(){
    $('.showHideCols_btn').hide();
    change_css();
    $('.accept').each(function(){ hide_accept_btn($(this)); });


    setInterval(function(){
        refresh_checking_orders_datatable($("#collapse_card_checking_orders table.table tbody"));
    },4000);

    if($('#user_type').val() == 'admin') {
        setInterval(function () {
            refresh_checking_transfers_datatable($("#collapse_card_checking_transfers table.table tbody"));
        }, 4000);
    }

    $('#refresh_btn_card_chargings').click(function() {
        refresh_chargings_datatable($("#collapse_card_chargings table.table tbody"));
    });
});

function change_css(){
    $('.child-card-header-color').css("background-color", "#4f4285");
    $('.card-child').css("margin", "10px");
    $('#content_div').css("margin-bottom", "70px");
    $('#collapse_card_chargings').removeClass('show')
}

function send_result($tr){
    var user_id = $tr.getElementsByTagName('td')[7].textContent;
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
        url: '/get_unavailable_packets_by_user',
        type: 'POST',
        data: {user_id: user_id},
        dataType: 'JSON',
        success: function (packet_ids) { 
            console.log('s');
            var order_id = $tr.getElementsByTagName('td')[1].textContent;
            var customer_name = $tr.getElementsByTagName('td')[3].textContent;
            var mobile = $tr.getElementsByTagName('td')[4].textContent;
            var operator = $tr.getElementsByTagName('td')[6].textContent;
        
            $('#id').val(order_id);
            $('#customer_name').val(customer_name);
            $('#mobile').val(mobile);
            $( ".packet" ).prop( "checked", false );
            $('.operator_packets').hide();
            $('#'+operator+'_packets').show();
            $( ".checkbox_div" ).show();

            packet_ids.forEach(function(packet_id) {console.log(packet_id);
                $( "#checkbox_div_"+packet_id['packet_id'] ).hide();
            });
        
            $('#modal_send_resultLabel').text(LANGS['HOME']['checking_offers_model_label']);
            $('#submit_add_btn').val(LANGS['HOME']['send']);
            $("#modal_send_result").modal("show");
        }
    });
}

function change_status($tr, status){
    var order_id = $tr.getElementsByTagName('td')[1].textContent;
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
        url: '/change_order_status_by_id',
        type: 'POST',
        data: {order_id: order_id,
                status: status},
        dataType: 'JSON'
    });

    window.location.href = '/home';
}

function change_charging_status($tr, status){
    var charging_id = $tr.getElementsByTagName('td')[1].textContent;
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
        url: '/change_charging_status_by_id',
        type: 'POST',
        data: {charging_id: charging_id,
                status:     status},
        dataType: 'JSON',
        success: function (data) {
            if(data['is_fail'])
                Swal(data['message']);
            else
                window.location.href = '/home';
        }
    });
}

function select_all(){
    $('.operator_packets').each(function (){
        if($(this).is(':visible'))
            $(this).find(".packet").prop( "checked", true );
    });
}

function unselect_all(){
    $('.operator_packets').each(function (){
        if($(this).is(':visible'))
            $(this).find(".packet").prop( "checked", false );
    });
}

function select_teen_packet(){
    unselect_all();
    $('.operator_packets').each(function (){
        if($(this).is(':visible'))
            $(this).find(".teen_is_1").prop( "checked", true );
    });
}

function hide_accept_btn(accept_btn){
    
    order_status = accept_btn.parent().parent().children("td:eq(2)").text();
    if(order_status == 'in_progress')
        accept_btn.hide();

}

function make_packet_in_transfer_status($tr){
    var order_id = $tr.children('td').eq(1).text();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/make_packet_in_transfer_status_for_regular',
        type: 'POST',
        data: {order_id: order_id},
        dataType: 'JSON',
        success: function(data) {
            if(data['is_fail'])
                Swal(data['message']);
            else
                window.location.href = '/'+data['toPage'];
        }
    });
}

function refresh_checking_orders_datatable(table_body){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/get_checking_orders_table',
        type: 'POST',
        data: {},
        dataType: 'JSON',
        success: function(table){
            table_body.empty();
            var tr_class = '';
            $.each(table, function(i, row) {
                tr_class = i%2 == 0?'odd':'even';
                table_body.append(
                    "<tr  role='row' class='"+tr_class+"'>" +
                        "<td>" + (i+1) + "</td>" +

                        "<td style='display: none;' class='sorting_1'>" + row['id'] + "</td>" +
                        "<td style='text-align:center;'>" + (row['name_of_user'] == null?'':row['name_of_user']) + "</td>" +
                        "<td style='text-align:center;'>" + (row['customer_name'] == null?'':row['customer_name']) + "</td>" +
                        "<td style='text-align:center;'>" + (row['mobile'] == null?'':row['mobile']) + "</td>" +
                        "<td style='text-align:center;'>" + (row['operator'] == null?'':row['operator']) + "</td>" +
                        "<td style='text-align:center;'>" + (row['request_date'] == null?'':row['request_date']) + "</td>" +
                        "<td style='display: none;'>" + row['operator_hidden'] + "</td>" +
                        "<td style='display: none;'>" + row['user_id'] + "</td>" +
                        "<td style='text-align:center;'>" + (row['message'] == null?'':row['message']) + "</td>" +
                        "<td style='text-align:center; padding-right: 4px ; padding-left: 4px;'>" + row['btn1'] + "</td>" +
                        "<td style='text-align:center; padding-right: 4px ; padding-left: 4px;'>" + row['btn2'] + "</td>" +

                    "</tr>"
                );
            });
        }
    });
}

function refresh_checking_transfers_datatable(table_body){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/get_checking_transfers_table',
        type: 'POST',
        data: {},
        dataType: 'JSON',
        success: function(table){
            table_body.empty();
            var tr_class = '';
            $.each(table, function(i, row) {
                tr_class = i%2 == 0?'odd':'even';
                table_body.append(
                    "<tr  role='row' class='"+tr_class+"'>" +
                    "<td>" + (i+1) + "</td>" +

                    "<td style='display: none;' class='sorting_1'>" + row['id'] + "</td>" +
                    "<td style='display: none;'>" + row['status_hidden'] + "</td>" +
                    "<td style='text-align:center;'>" + (row['name_of_user'] == null?'':row['name_of_user']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['mobile'] == null?'':row['mobile']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['operator'] == null?'':row['operator']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['packet_name'] == null?'':row['packet_name']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['purchasing_price'] == null?'':row['purchasing_price']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['selling_price'] == null?'':row['selling_price']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['profit'] == null?'':row['profit']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['request_date'] == null?'':row['request_date']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['status'] == null?'':row['status']) + "</td>" +
                    "<td style='text-align:center; padding-right: 4px ; padding-left: 4px;'>" + row['btn1'] + "</td>" +
                    "<td style='text-align:center; padding-right: 4px ; padding-left: 4px;'>" + row['btn2'] + "</td>" +
                    "<td style='text-align:center; padding-right: 4px ; padding-left: 4px;'>" + row['btn3'] + "</td>" +

                    "</tr>"
                );
            });
            $('.accept').each(function(){ hide_accept_btn($(this)); });
        }
    });
}

function refresh_chargings_datatable(table_body){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/get_chargings_table',
        type: 'POST',
        data: {},
        dataType: 'JSON',
        success: function(table){
            table_body.empty();
            var tr_class = '';
            $.each(table, function(i, row) {
                tr_class = i%2 == 0?'odd':'even';
                table_body.append(
                    "<tr  role='row' class='"+tr_class+"'>" +
                    "<td>" + (i+1) + "</td>" +

                    "<td style='display: none;' class='sorting_1'>" + row['id'] + "</td>" +
                    "<td style='text-align:center;'>" + (row['user'] == null?'':row['user']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['type'] == null?'':row['type']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['amount'] == null?'':row['amount']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['balance_before'] == null?'':row['balance_before']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['balance_after'] == null?'':row['balance_after']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['notes'] == null?'':row['notes']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['request_date'] == null?'':row['request_date']) + "</td>" +
                    "<td style='text-align:center; padding-right: 4px ; padding-left: 4px;'>" + row['btn1'] + "</td>" +
                    "<td style='text-align:center; padding-right: 4px ; padding-left: 4px;'>" + row['btn2'] + "</td>" +

                    "</tr>"
                );
            });
        }
    });
}
