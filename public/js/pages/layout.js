// $.ajaxSetup({
//     headers: {
//         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//     }
// });

$(document).ready(function(){
    events();
    change_checkbox_value();
});

function events(){
    $('.lang').click(function(){changeLang($(this).attr('val'));});
}

function changeLang(lng){   // This function for change website's language
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/change_lang',
        type: 'POST',
        data: {lang: lng},
        dataType: 'JSON'
    });
}

function change_checkbox_value(){
    $(':checkbox').click(function() {
        if($(this).hasClass('checked'))
          $(this).removeClass('checked')
        else
          $(this).addClass('checked')
      });
}