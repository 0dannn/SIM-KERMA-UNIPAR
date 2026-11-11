$(document).ready(function(){
    $('form#login input').on('change', function(){
        $(this).removeClass('is-invalid');  
        $(this).next().next().text('');
    });

    $('form#login').on('submit', function(e){
        console.log('login');
        e.preventDefault();
        e.stopImmediatePropagation();

        var infobox = $('#infoMessage');
        infobox.addClass('callout callout-info').text('Checking...');

        var btnsubmit = $('#submit');
        btnsubmit.attr('disabled', 'disabled').val('Wait...');

        $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        success: function(data){
            infobox.removeAttr('class').text('');
            btnsubmit.removeAttr('disabled').val('Login');
            if(data.status){
                infobox.addClass('alert alert-success text-center').text('Login Sukses');
                var go = base_url + data.url;
                window.location.href = go;
            }else{
                if(data.invalid){
                    $.each(data.invalid, function(key, val){
                    $('[name="'+key+'"').addClass('is-invalid');
                    $('[name="'+key+'"').next().next().html(val);
                    if(val == ''){
                        $('[name="'+key+'"').removeClass('is-invalid');  
                        $('[name="'+key+'"').next().next().html('');
                    }
                    });
                }
                    if(data.failed){
                        infobox.addClass('alert alert-danger text-center').text(data.failed);
                    }
                }
            }
        });
    });
});