$(document).ready(function(){
    var table = $('table.table').DataTable();
    $('#btn_add').on( 'click', function(){add_click();});
    $('#btn_edit').on( 'click', function(){edit_click(table);});
    $('#btn_del').on( 'click', function(){del_click(table);});
});

function add_click(){
    $('#submit_add_btn').show();
    $('#submit_edit_btn').hide();

    $('#operator').val('');
    $('#post_type').val('add');
    
    $('#modal_addEditLabel').text(LANGS['DATA_TABLE']['add']);
    $("#modal_addEdit").modal("show");
}

function edit_click(table){
    if(table.rows('.selected').data().length == 0){
        Swal(LANGS['DATA_TABLE']['row_select_warning']);
    }else{
        operator = table.rows('.selected').data()[0][1];

        if(operator == 'turkcell' || operator == 'vodafone'){
            Swal(LANGS['DATA_TABLE']['protected_element_warning']);
            return;
        }

        $('#submit_add_btn').hide();
        $('#submit_edit_btn').show();
    
        $('#old_operator').val(operator);
        $('#operator').val(operator);
        $('#post_type').val('edit');

        $('#modal_addEditLabel').text(LANGS['DATA_TABLE']['edit']);
        $("#modal_addEdit").modal("show");
    }
}

function del_click(table){
    if(table.rows('.selected').data().length == 0){
        Swal(LANGS['DATA_TABLE']['row_select_warning']);
    }else{
        if(table.rows('.selected').data()[0][1] == 'turkcell' || table.rows('.selected').data()[0][1] == 'vodafone'){
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
                var operator = table.rows('.selected').data()[0][1];
                $('#form_del').attr('action', 'operators/'+operator);
                $( "#form_del" ).submit();
            }
        })
    }
}