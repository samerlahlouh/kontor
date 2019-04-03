$(document).ready(function(){
    var table = $('table.table').DataTable();
    $('#btn_add').on( 'click', function(){add_click();});
    $('#btn_edit').on( 'click', function(){edit_click(table);});
    $('#btn_del').on( 'click', function(){del_click(table);});
});

function add_click(){
    $('#submit_add_btn').show();
    $('#submit_edit_btn').hide();

    $('#post_type').val('add');
    $('#id').val('');
    $('#name').val('');
    $('#description').val('');


    $('#modal_addEditLabel').text(LANGS['DATA_TABLE']['add']);
    $("#modal_addEdit").modal("show");
}

function edit_click(table){
    if(table.rows('.selected').data().length == 0){
        Swal(LANGS['DATA_TABLE']['row_select_warning']);
    }else{
        var id = table.rows('.selected').data()[0][1];
        var name = table.rows('.selected').data()[0][2];
        var description = table.rows('.selected').data()[0][3];

        $('#submit_add_btn').hide();
        $('#submit_edit_btn').show();

        $('#post_type').val('edit');
        $('#id').val(id);
        $('#name').val(name);
        $('#description').val(description);

        $('#modal_addEditLabel').text(LANGS['DATA_TABLE']['edit']);
        $("#modal_addEdit").modal("show");
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
        }).then(function(result) {
            if (result.value) {
            var id = table.rows('.selected').data()[0][1];
            $('#form_del').attr('action', 'groups/'+id);
            $( "#form_del" ).submit();
            }
        })
    }
}

function show_packets($tr){
    var $table = $('table.table').DataTable();
    var group_id = $table.row($tr).data()[1];
    window.location.href = '/group_packets/'+group_id;
}

function show_users($tr){
    var $table = $('table.table').DataTable();
    var group_id = $table.row($tr).data()[1];
    window.location.href = '/group_users/'+group_id;
}

function synchronize_users($tr){
    swal({
        title: LANGS['GROUPS']['are_you_sure'],
        text: LANGS['GROUPS']['no_revert'],
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: LANGS['GROUPS']['cancel'],
        confirmButtonText: LANGS['GROUPS']['del_accepted']
    }).then(function(result) {
        if (result.value) {
            var table = $('table.table').DataTable();
            var group_id = table.rows('.selected').data()[0][1];
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/synchronize_users',
                type: 'POST',
                data: {group_id: group_id},
                dataType: 'JSON',
                success: function(){
                    document.location.reload();
                }
            });
        }
    })
}

