$(document).ready(function(){
    var table = $('table.table').DataTable();
    $('#btn_add').on( 'click', function(){add_click();});
    $('#btn_edit').on( 'click', function(){edit_click(table);});
    $('#btn_del').on( 'click', function(){del_click(table);});
});

function add_click(){
    $('#submit_add_btn').show();
    $('#submit_edit_btn').hide();

    $('#id').val('');
    $('#operator').val(0);
    $('#sms').val('');
    $('#minutes').val('');
    $('#internet').val('');
    $('#type').val(0);
    $('#price').val('');
    $('#is_global').val(0);
    $('#is_teens').val(0);
    
    $('#modal_addEditLabel').text(LANGS['DATA_TABLE']['add']);
    $("#modal_addEdit").modal("show");
}

function edit_click(table){
    if(table.rows('.selected').data().length == 0){
        Swal(LANGS['DATA_TABLE']['row_select_warning']);
    }else{
        id = table.rows('.selected').data()[0][1];
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/get_packet',
            type: 'POST',
            data: {id: id},
            dataType: 'JSON',
            success: function (packet) { 
                $('#submit_edit_btn').show();
                $('#submit_add_btn').hide();

                $('#id').val(packet['id']);
                $('#operator').val(packet['operator']);
                $('#sms').val(packet['sms']);
                $('#minutes').val(packet['minutes']);
                $('#internet').val(packet['internet']);
                $('#type').val(packet['type']);
                $('#price').val(packet['price']);
                $('#is_global').val(packet['is_global']+1);
                $('#is_teens').val(packet['is_teens']+1);

                $('#modal_addEditLabel').text(LANGS['DATA_TABLE']['edit']);
                $("#modal_addEdit").modal("show");
            }
        });
    }
}

function del_click(table){
    if(table.rows('.selected').data().length == 0){
        Swal(LANGS['DATA_TABLE']['row_select_warning']);
    }else{
        swal({
            title: LANGS['DATA_TABLE']['are_you_sure'],
            text: LANGS['DATA_TABLE']['no_revert'],
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: LANGS['DATA_TABLE']['cancel'],
            confirmButtonText: LANGS['DATA_TABLE']['del_accepted']
            }).then((result) => {
            if (result.value) {
                var id = table.rows('.selected').data()[0][1];
                $('#form_del').attr('action', 'packets/'+id);
                $( "#form_del" ).submit();
            }
        })
    }
}