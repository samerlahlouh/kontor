$(document).ready(function(){
    var table = $('table.table').DataTable();
    $('#btn_add').on( 'click', function(){add_click();});
    $('#btn_del').on( 'click', function(){del_click(table);});

    //Activate and Deactivate button format
    $('.activator').each(function(){
        if(true){
            var $tr = $(this).parent().parent()
            var is_active = table.row($tr).data()[2];
            if(is_active == 1){
                $(this).addClass('btn-danger');
                $(this).html('Deactivate <i class="fa fa-user-times" aria-hidden="true"></i>');
                $(this).attr("onclick","deactivate_user(this.parentNode.parentNode)");
            }else if(is_active == 0){
                $(this).addClass('btn-success');
                $(this).html('Activate <i class="fa fa-user-plus" aria-hidden="true"></i>');
                $(this).attr("onclick","activate_user(this.parentNode.parentNode)");
            }
        }
    });
});

function add_click(){
    $('#submit_add_btn').show();
    $('#submit_edit_btn').hide();
    
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
                var id = table.rows('.selected').data()[0][1];
                $('#form_del').attr('action', 'users/'+id);
                $( "#form_del" ).submit();
            }
        })
    }
}

function deactivate_user($tr){
    var $table = $('table.table').DataTable();
    var user_id = $table.row($tr).data()[1];
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/deactivate_user',
        type: 'POST',
        data: {user_id: user_id},
        dataType: 'JSON'
    });
    document.location.reload();
}

function activate_user($tr){
    var $table = $('table.table').DataTable();
    var user_id = $table.row($tr).data()[1];
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/activate_user',
        type: 'POST',
        data: {user_id: user_id},
        dataType: 'JSON'
    });
    document.location.reload();
}