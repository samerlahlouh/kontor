$(document).ready(function(){
    var table = $('table.table').DataTable();
    $('#btn_edit').on( 'click', function(){edit_click(table);});
});

function edit_click(table){
    if(!$('.checked-row').hasClass('checked')){
        Swal(LANGS['DATA_TABLE']['row_select_warning']);
    }else{
        var userPacketIds = '', userPacketId, tr;
        $('.checked-row').each(function(){
            if($(this).hasClass('checked')){
                tr = $(this).parent().parent().parent();
                userPacketId = table.row(tr).data()[2];
                userPacketIds += '_' +userPacketId;
            }
        })
        $('#ids').val(userPacketIds);
        $('#user_price').val('');

        $('#submit_edit_btn').show();
        $('#submit_add_btn').hide();
        $('#modal_addEditLabel').text(LANGS['DATA_TABLE']['edit']);
        $("#modal_addEdit").modal("show");
    }
}