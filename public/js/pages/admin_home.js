$(document).ready(function(){
    $('.showHideCols_btn').hide();
    change_css();
    $('.accept').each(function(){ hide_accept_btn($(this)); });
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