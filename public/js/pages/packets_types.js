$(document).ready(function(){
    var table = $('table.table').DataTable();
    $('#btn_add').on( 'click', function(){add_click();});
    $('#btn_edit').on( 'click', function(){edit_click(table);});
    $('#btn_del').on( 'click', function(){del_click(table);});
});

function add_click(){
    $('#submit_add_btn').show();
    $('#submit_edit_btn').hide();

    $('#type').val('');
    $('#post_type').val('add');
    
    $('#modal_addEditLabel').text(LANGS['DATA_TABLE']['add']);
    $("#modal_addEdit").modal("show");
}

function edit_click(table){
    if(table.rows('.selected').data().length == 0){
        Swal(LANGS['DATA_TABLE']['row_select_warning']);
    }else{
        type = table.rows('.selected').data()[0][1];

        if(type == 'combo' || type == 'internet' || type == 'minutes' || type == 'packet' || type == 'tl'){
            Swal(LANGS['DATA_TABLE']['protected_element_warning']);
            return;
        }

        $('#submit_add_btn').hide();
        $('#submit_edit_btn').show();
    
        $('#old_type').val(type);
        $('#type').val(type);
        $('#post_type').val('edit');

        $('#modal_addEditLabel').text(LANGS['DATA_TABLE']['edit']);
        $("#modal_addEdit").modal("show");
    }
}

function del_click(table){
    if(table.rows('.selected').data().length == 0){
        Swal(LANGS['DATA_TABLE']['row_select_warning']);
    }else{
        if( table.rows('.selected').data()[0][1] == 'combo' ||
            table.rows('.selected').data()[0][1] == 'internet' ||
            table.rows('.selected').data()[0][1] == 'minutes' ||
            table.rows('.selected').data()[0][1] == 'packet' ||
            table.rows('.selected').data()[0][1] == 'tl'){
            Swal(LANGS['DATA_TABLE']['protected_element_warning']);
            return;
        }
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
                var type = table.rows('.selected').data()[0][1];
                $('#form_del').attr('action', 'packets_types/'+type);
                $( "#form_del" ).submit();
            }
        })
    }
}