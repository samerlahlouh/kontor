$(document).ready(function(){
    var table = $('table.table').DataTable();
    $('#btn_add').on( 'click', function(){add_click();});
    $('#btn_edit').on( 'click', function(){edit_click(table);});
    $('#btn_del').on( 'click', function(){del_click(table);});

    $('#submit_edit_btn').on('click', function(){
        $("#operator").prop('disabled', false);
    })
});

function add_click(){
    $('#submit_add_btn').show();
    $('#submit_edit_btn').hide();

    $("#operator").prop('disabled', false);
    $('#operator').val('');
    $('#api_user_name').val('');
    $('#api_password').val('');
    $('#api_operator').val('');
    $('#site_url').val('');
    $( "#is_api" ).prop( "checked", false );
    $('#post_type').val('add');

    $('#modal_addEditLabel').text(LANGS['DATA_TABLE']['add']);
    $("#modal_addEdit").modal("show");
}

function edit_click(table){
    if(table.rows('.selected').data().length == 0){
        Swal(LANGS['DATA_TABLE']['row_select_warning']);
    }else{
        operator = table.rows('.selected').data()[0][1];
        api_user_name = table.rows('.selected').data()[0][2];
        api_password = table.rows('.selected').data()[0][3];
        api_operator = table.rows('.selected').data()[0][4];
        is_api = table.rows('.selected').data()[0][7];
        site_url = table.rows('.selected').data()[0][6];

        $('#submit_add_btn').hide();
        $('#submit_edit_btn').show();

        $("#operator").prop('disabled', true);
        $('#old_operator').val(operator);
        $('#operator').val(operator);
        $('#api_user_name').val(api_user_name);
        $('#api_password').val(api_password);
        $('#api_operator').val(api_operator);
        $('#post_type').val('edit');
        $( "#is_api" ).prop( "checked", is_api );
        $( "#site_url" ).val(site_url);

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

function show_operator_types($tr){
    var $table = $('table.table').DataTable();
    var operator = $table.row($tr).data()[1];
    window.location.href = '/operator_types/'+operator;
}
