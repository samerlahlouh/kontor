$(document).ready(function(){
    var table = $('table.table').DataTable();
    $('#btn_add').on( 'click', function(){add_click();});
    $('#btn_del').on( 'click', function(){del_click(table);});
});

function add_click(){
    $('#submit_add_btn').show();
    $('#submit_edit_btn').hide();

    $('#old_type').val('');
    $('#post_type').val('add');
    $('#type').val(0);

    $('#modal_addEditLabel').text(LANGS['DATA_TABLE']['add']);
    $("#modal_addEdit").modal("show");
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
            var type = table.rows('.selected').data()[0][1];
            var operator = $('#operator').val();
            $('#form_del').attr('action', operator + "_" + type);
            $( "#form_del" ).submit();
        }
    })
    }
}
