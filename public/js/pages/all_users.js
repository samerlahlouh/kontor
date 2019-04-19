$(document).ready(function(){
    var table = $('table.table').DataTable();
    //Activate and Deactivate button format
    $('.activator').each(function(){
        var $tr = $(this).parent().parent()
        var is_active = table.row($tr).data()[2];
        if(is_active == 1){
            $(this).addClass('btn-danger');
            $(this).html(LANGS['USERS']['deactivate']+' <i class="fa fa-user-times" aria-hidden="true"></i>');
            $(this).attr("onclick","deactivate_user(this.parentNode.parentNode)");
        }else if(is_active == 0){
            $(this).addClass('btn-success');
            $(this).html(LANGS['USERS']['activate']+' <i class="fa fa-user-plus" aria-hidden="true"></i>');
            $(this).attr("onclick","activate_user(this.parentNode.parentNode)");
        }
    });

    $('.is_checking_free').each(function(){
        var $tr = $(this).parent().parent()
        var is_checking_free = table.row($tr).data()[10];
        if(is_checking_free == 1){
            $(this).addClass('btn-danger');
            $(this).html(LANGS['USERS']['pay']);
            $(this).attr("onclick","make_checking_paid(this.parentNode.parentNode)");
        }else if(is_checking_free == 0){
            $(this).addClass('btn-success');
            $(this).html(LANGS['USERS']['free']);
            $(this).attr("onclick","make_checking_free(this.parentNode.parentNode)");
        }
    });
});



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
        dataType: 'JSON',
        success: function(){
            document.location.reload();
        }
    });
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
        dataType: 'JSON',
        success: function(){
            document.location.reload();
        }
    });
}

function make_checking_paid($tr){
    var $table = $('table.table').DataTable();
    var user_id = $table.row($tr).data()[1];
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/make_checking_paid',
        type: 'POST',
        data: {user_id: user_id},
        dataType: 'JSON',
        success: function(){
            document.location.reload();
        }
    });
}

function make_checking_free($tr){
    var $table = $('table.table').DataTable();
    var user_id = $table.row($tr).data()[1];
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/make_checking_free',
        type: 'POST',
        data: {user_id: user_id},
        dataType: 'JSON',
        success: function(){
            document.location.reload();
        }
    });
}