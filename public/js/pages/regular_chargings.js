$(document).ready(function(){
    var table = $('table.table').DataTable();
    $('#btn_add').on( 'click', function(){add_click();});

    //Cancel button format
    $('.cancel-btn').each(function(){
        var $tr = $(this).parent().parent()
        var is_accepted = table.row($tr).data()[2] == 'accepted'?true:false;
        if(is_accepted){
            $(this).hide();
        }
    });
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