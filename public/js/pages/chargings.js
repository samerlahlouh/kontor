$(document).ready(function(){
    var table = $('table.table').DataTable();
    $('#btn_add').on( 'click', function(){add_click();});
    $('#btn_edit').on( 'click', function(){edit_click(table);});
    $('#btn_del').on( 'click', function(){del_click(table);});

    $( "#type" ).change(function() {
        var type_value = $(this).val();
        if(type_value == 'pay_off'){
            $('#status').parent().parent().parent().hide();
            $('#status').val('accepted');
        }else
            $('#status').parent().parent().parent().show();
            
      });
});

function add_click(){
    $('#submit_add_btn').show();
    $('#submit_edit_btn').hide();
    $('#status').parent().parent().parent().show();

    $("#user_id").removeAttr('disabled');
    $("#type").removeAttr('disabled');
    $('#id').val('');
    $('#user_id').val(0);
    $('#type').val(0);
    $('#status').val(0);
    $('#amount').val(0);
    set_date_to_be_today('request_date');
    $('#response_date').val('');
    $('#notes').val('');
    
    $('#modal_addLabel').text(LANGS['DATA_TABLE']['add']);
    $("#modal_add").modal("show");
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
            url: '/get_charging',
            type: 'POST',
            data: {id: id},
            dataType: 'JSON',
            success: function (charging) { 
                $('#modal_edit #id').val(charging['id']);
                $('#modal_edit #request_date').val(charging['request_date']);
                $('#modal_edit #response_date').val(charging['response_date']);
                $('#modal_edit #notes').val(charging['notes']);

                $('#modal_editLabel').text(LANGS['DATA_TABLE']['edit']);
                $("#modal_edit").modal("show");
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
                $('#form_del').attr('action', 'chargings/'+id);
                $( "#form_del" ).submit();
            }
        })
    }
}

function set_date_to_be_today(input_element_id){
    var now = new Date();

    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);

    var today = now.getFullYear()+"-"+(month)+"-"+(day) ;

    $('#'+input_element_id).val(today);
}