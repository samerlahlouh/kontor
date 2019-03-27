$(document).ready(function(){
    var table = $('table.table').DataTable();
    $('#btn_add').on( 'click', function(){add_click();});
    $('.cancel-btn').each(function(){hide_cancel_btn($(this))});
});

function add_click(){
    $('#submit_add_btn').show();
    $('#submit_edit_btn').hide();

    $('#type').val(0);
    $('#amount').val('');
    $('#notes').val('');
    
    $('#modal_addEditLabel').text(LANGS['DATA_TABLE']['add']);
    $("#modal_addEdit").modal("show");
}

function charging_cancel($tr){
    var $table = $('table.table').DataTable();
    var charging_id = $table.row($tr).data()[1];
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/delete_charging',
        type: 'POST',
        data: {charging_id: charging_id},
        dataType: 'JSON'
    });
    window.location.href = '/regular_chargings';
}

function hide_cancel_btn(btn){
    var $tr = btn.parent().parent();
    var status = btn.parent().parent().children("td:eq(2)").text();
    if(status != 'in_waiting'){
        btn.hide();
    }
}