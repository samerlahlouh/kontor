$(document).ready(function(){
    $('.showHideCols_btn').hide();
    // $('#btn_transfer').on( 'click', function(){add_click();});
    // $( "#operator" ).change(function() {operator_select_changed($(this));});
    $( "#type" ).change(function() {type_select_changed($(this));});

    $('.transfer').each(function(){ hide_transfer_btns($(this)); });
    $('.cancel').each(function(){ hide_cancel_btns($(this)); });
    $('.checking_order_cancel').each(function(){ hide_checking_order_cancel_btns($(this)); });
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
            data: {operator: operator},
            dataType: 'JSON',
            success: function (packets) { 
                var output = [];
                output.push('<option value="0" hidden disabled selected>'+ LANGS['HOME']['packet'] +'</option>');
                $.each(packets, function(key, value){
                    output.push('<option value="'+ key +'">'+ value +'</option>');
                });
                $('#packet').html(output.join(''));

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
            dataType: 'JSON'
        });
        
        window.location.href = '/home';
    }
}

function type_select_changed($type_select){
    $('#packet').val(0);
    var operator = $('#operator').val();
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
        success: function (packets) { 
            var output = [];
            output.push('<option value="0" hidden disabled selected>'+ LANGS['HOME']['packet'] +'</option>');
            $.each(packets, function(key, value){
                output.push('<option value="'+ key +'">'+ value +'</option>');
            });
            $('#packet').html(output.join(''));
        }
    });
}

function maxLengthCheck(object)
  {
    if (object.value.length > object.maxLength)
      object.value = object.value.slice(0, object.maxLength)
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
        dataType: 'JSON'
    });

    window.location.href = '/home';
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
        dataType: 'JSON'
    });

    window.location.href = '/home';
}

function hide_cancel_btns(cancel_btn){
    order_status = cancel_btn.parent().parent().children("td:eq(2)").text();
    if(order_status != 'in_review')
        cancel_btn.hide();
}