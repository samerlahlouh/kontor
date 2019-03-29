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

function show_notes($tr){
    var $table = $('table.table').DataTable();
    var packet_id = $table.row($tr).data()[6];

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/get_notes_of_packet',
        type: 'POST',
        data: {packet_id: packet_id},
        dataType: 'JSON',
        success: function (notes) { 
            $.sweetModal({
                title: {
                    tab1: {
                        label: LANGS['PACKETS']['notes'],
                        icon: '<i class="fa fa-list-alt" aria-hidden="true"></i>'
                    }
                },
            
                content: {
                    tab1: notes
                },
                theme: $.sweetModal.THEME_DARK
            });
        }
    });
}