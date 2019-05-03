var isSelectActive = false ;
var stopAnyWay = false ;
$(document).ready(function(){
    $('.stop-refresh').hide();
    $('.showHideCols_btn').hide();
    $( "#type" ).change(function() {type_select_changed($(this));});

    $('.transfer').each(function(){ hide_transfer_btns($(this)); });
    $('.cancel').each(function(){ hide_cancel_btns($(this)); });
    $('.checking_order_cancel').each(function(){ hide_checking_order_cancel_btns($(this)); });


    setInterval(function(){
                            refresh_checking_orders_datatable($("#panel_checking_orders table.table tbody"));
                        },4000);
    $(document).delegate('#panel_checking_orders select', 'mouseenter', function() { stop_checking_order_interval();});
    $(document).delegate('#panel_checking_orders select', 'mouseleave', function() { play_checking_order_interval();});
    $(document).delegate('#panel_checking_orders select', 'change', function() { stop_checking_order_interval_any_way();});

    setInterval(function(){
        refresh_checking_transfers_datatable($("#panel_checking_transfers table.table tbody"));
    },4000);

    $('.play-refresh').click(function() { stop_checking_order_interval_any_way(); });
    $('.stop-refresh').click(function() { play_checking_order_interval_any_way(); });
});

function add_click(){
    if($('#number').val() == '')
        Swal(LANGS['HOME']['fill_blanks_warning']);
    else if($('#number').val().length < 10)
        Swal(LANGS['HOME']['number_correctly_warning']);
    else{
        var operator = $('#selected_operator').val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
            url: '/get_packets_by_operator_and_type',
            type: 'POST',
            data: {
                    operator    : operator,
                    is_global   : 1},
            dataType: 'JSON',
            success: function ($res) {
                var output = [];
                output.push('<option value="0" hidden disabled selected>'+ LANGS['HOME']['packet'] +'</option>');
                $.each($res['packets'], function(key, value){
                    output.push('<option value="'+ key +'">'+ value +'</option>');
                });
                $('#packet').html(output.join(''));

                output = [];
                output.push('<option value="0" hidden disabled selected>'+ LANGS['HOME']['type'] +'</option>');
                $.each($res['types'], function(key, value){
                    output.push('<option value="'+ key +'">'+ value +'</option>');
                });
                $('#type').html(output.join(''));

                $('#operator').val(operator);
                $('#type').val(0);
                $('#packet').val(0);
                $('#mobile').val($('#number').val());
                $('#customer').val($('#customer_name').val());

                $('#modal_transferLabel').text(LANGS['HOME']['transfer_packet']);
                $('#submit_add_btn').val(LANGS['HOME']['transfer']);
                $("#modal_transfer").modal("show");
            }
        });
    }
}

function check_number(){
    if($('#number').val() == '' || !$('#selected_operator').val()){
        Swal(LANGS['HOME']['fill_blanks_warning']);
    }else if($('#number').val().length < 10){
        Swal(LANGS['HOME']['number_correctly_warning']);
    }else{
        number          = $('#number').val();
        customer_name   = $('#customer_name').val();
        operator        = $('#selected_operator').val();
        message         = $('#message').val();
            
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/check_number',
            type: 'POST',
            data: {number:      number,
                customer_name:  customer_name,
                operator:       operator,
                message:        message},
            dataType: 'JSON',
            success: function (to_page) {
                window.location.href = '/'+to_page;
            },
            error: function (data) {
                var errors = data.responseJSON;
                Swal(errors[0]);
            }
        });
        

    }
}

function type_select_changed($type_select){
    $('#packet').val(0);
    var operator = $('#selected_operator').val();
    var type = $type_select.val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
        url: '/get_packets_by_operator_and_type',
        type: 'POST',
        data: {operator : operator,
                type    : type},
        dataType: 'JSON',
        success: function (res) {
            var output = [];
            output.push('<option value="0" hidden disabled selected>'+ LANGS['HOME']['packet'] +'</option>');
            $.each(res['packets'], function(key, value){
                output.push('<option value="'+ key +'">'+ value +'</option>');
            });
            $('#packet').html(output.join(''));
        }
    });
}

function mobileFormat (object){
    var mobile = object.value;
    mobile = mobile.replace(/\s/g,'');
    while(mobile[0] == '0')
        mobile = mobile.slice(1);

    if (mobile.length > object.maxLength)
        mobile = mobile.slice(0, object.maxLength);

    object.value = mobile;
}

function cancel_order($tr, id_index){
    var order_id = $tr.getElementsByTagName('td')[id_index].textContent;

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
        url: '/cancel_order_by_id',
        type: 'POST',
        data: {order_id: order_id},
        dataType: 'JSON',
        success: function (data) {
            if(data['fail'])
                Swal(data['message']);
            else
                window.location.href = '/'+data['toPage'];

        }
    });
}

function hide_transfer_btns(transfer_btn){
    
    order_status = transfer_btn.parent().parent().children("td:eq(3)").text();
    if(order_status != 'selecting_packet')
        transfer_btn.hide();

}

function make_packet_in_transfer_status($tr){
    var order_id = $tr.children('td').eq(2).text();
    var selected_packet_id = $tr.children('td:eq(1)').children('div:eq(0)').children('select:eq(0)').val();

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
        url: '/make_packet_in_transfer_status',
        type: 'POST',
        data: {order_id: order_id,
                selected_packet_id: selected_packet_id},
        dataType: 'JSON',
        success: function(data) {
            if(data['is_fail'])
                Swal(data['message']);
            else
                window.location.href = '/'+data['toPage'];
        }
    });
}

function hide_cancel_btns(cancel_btn){
    order_status = cancel_btn.parent().parent().children("td:eq(2)").text();
    if(order_status != 'in_review')
        cancel_btn.hide();
}

function refresh_checking_orders_datatable(table_body){
    if(stopAnyWay || isSelectActive)
        return;
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/get_regular_checking_orders_table',
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
                    "<td style='text-align:center; padding-right: 4px ; padding-left: 4px;' class='sorting_1'>" + row['selected_packet'] + "</td>" +
                    "<td style='display: none;' class='sorting_1'>" + row['id'] + "</td>" +
                    "<td style='display: none;'>" + row['status_hidden'] + "</td>" +
                    "<td style='display: none;'>" + row['operator_hidden'] + "</td>" +
                    "<td style='text-align:center;'>" + (row['customer_name'] == null?'':row['customer_name']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['mobile'] == null?'':row['mobile']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['status'] == null?'':row['status']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['request_date'] == null?'':row['request_date']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['response_date'] == null?'':row['response_date']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['message'] == null?'':row['message']) + "</td>" +
                    "<td style='text-align:center; padding-right: 4px ; padding-left: 4px;'>" + row['btn1'] + "</td>" +
                    "<td style='text-align:center; padding-right: 4px ; padding-left: 4px;'>" + row['btn2'] + "</td>" +

                    "</tr>"
                );
            });
            $('.transfer').each(function(){ hide_transfer_btns($(this)); });
        }
    });
}

function refresh_checking_transfers_datatable(table_body){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/get_regular_checking_transfers_table',
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
                    "<td style='text-align:center;'>" + (row['customer_name'] == null?'':row['customer_name']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['mobile'] == null?'':row['mobile']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['packet_name'] == null?'':row['packet_name']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['purchasing_price'] == null?'':row['purchasing_price']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['selling_price'] == null?'':row['selling_price']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['profit'] == null?'':row['profit']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['status'] == null?'':row['status']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['request_date'] == null?'':row['request_date']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['response_date'] == null?'':row['response_date']) + "</td>" +
                    "<td style='text-align:center; padding-right: 4px ; padding-left: 4px;'>" + row['btn'] + "</td>" +

                    "</tr>"
                );
            });
            $('.cancel').each(function(){ hide_cancel_btns($(this)); });
        }
    });
}

function stop_checking_order_interval() {
    isSelectActive = true;

    if(!stopAnyWay){
        $('.stop-refresh').show();
        $('.play-refresh').hide();
    }
}
function stop_checking_order_interval_any_way() {
    stopAnyWay = true;
    $('.stop-refresh').show();
    $('.play-refresh').hide();
}

function play_checking_order_interval() {
    isSelectActive = false;
    if(!stopAnyWay) {
        $('.stop-refresh').hide();
        $('.play-refresh').show();
    }
}
function play_checking_order_interval_any_way() {
    stopAnyWay = false;
    $('.stop-refresh').hide();
    $('.play-refresh').show();
}

