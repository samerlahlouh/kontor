$(document).ready(function(){
    var table = $('table.table').DataTable();
    $('#btn_add').on( 'click', function(){add_click();});
    $('#btn_edit').on( 'click', function(){edit_click(table);});
    $('#btn_del').on( 'click', function(){del_click(table);});
    $( "#operator" ).change(function() {operator_select_changed($(this));});
});

function add_click(){
    $('#submit_add_btn').show();
    $('#submit_edit_btn').hide();

    $('#id').val('');
    $('#name').val('');
    $('#api_id').val('');
    $('#operator').val('turkcell');
    $('#sms').val('');
    $('#minutes').val('');
    $('#internet').val('');
    $('#type').val(0);
    $('#price').val('');
    $('#is_global').val(2);
    $('#is_teens').val(1);
    $('#notes').val('');
    $( "#is_available_for_all" ).prop( "checked", true );
    
    $('#modal_addEditLabel').text(LANGS['DATA_TABLE']['add']);
    $('.row_is_available_for_all').show();
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
            success: function (res) {
                change_type_select_options(res['select_types']);

                $('#submit_edit_btn').show();
                $('#submit_add_btn').hide();

                $('#id').val(res['packet']['id']);
                $('#name').val(res['packet']['name']);
                $('#api_id').val(res['packet']['api_id']);
                $('#operator').val(res['packet']['operator']);
                $('#sms').val(res['packet']['sms']);
                $('#minutes').val(res['packet']['minutes']);
                $('#internet').val(res['packet']['internet']);
                $('#type').val(res['packet']['type']);
                $('#price').val(res['packet']['price']);
                $('#is_global').val(res['packet']['is_global']+1);
                $('#is_teens').val(res['packet']['is_teens']+1);
                $('#notes').val(res['packet']['notes']);

                $('#modal_addEditLabel').text(LANGS['DATA_TABLE']['edit']);
                $('.row_is_available_for_all').hide();
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

function show_packet_users($tr){
    var $table = $('table.table').DataTable();
    var packet_id = $table.row($tr).data()[1];
    window.location.href = '/packet_users/'+packet_id;
}

function show_notes($tr){
    var $table = $('table.table').DataTable();
    var packet_id = $table.row($tr).data()[1];

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

function operator_select_changed($operator_select){
    $('#type').val(0);
    var operator = $operator_select.val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/get_types_by_operator',
        type: 'POST',
        data: {operator : operator},
        dataType: 'JSON',
        success: function (types) {
            change_type_select_options(types);
        }
    });
}

function change_type_select_options(types) {
    var output = [];
    output.push('<option value="0" hidden disabled selected>'+ LANGS['PACKETS']['type'] +'</option>');
    $.each(types, function(key, value){
        output.push('<option value="'+ key +'">'+ value +'</option>');
    });
    $('#type').html(output.join(''));
}
