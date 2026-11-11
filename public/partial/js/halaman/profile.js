console.log('from profile');
$(document).ready(function(){
    $('.dropify').dropify({
        messages: {
            default: 'Drag atau drop untuk memilih gambar',
            replace: 'Ganti',
            remove: 'Hapus',
            error: 'error'
        }
    });
});

$('#profileForm').submit(function(e){
    var mode = $(this).find('input[name="mode"]').val();
    var msg = mode == 'add' ? 'disimpan' : 'diedit';
    $(this).find('input[name="'+csrfname+'"]').val($.cookie('csrf_cookie_name'));
    e.preventDefault();
    $.ajax({
        url: $(this).attr('action'),
        data: new FormData(this),
        type: 'post', dataType: 'json',
        processData: false,
        contentType: false,
        cache: false,
        async: false,
        beforeSend: function() {
            swal.fire({title: 'Menunggu',html: 'Memproses data',didOpen: () => {swal.showLoading()}})
        },
        success: function(data) {
            console.log(data);
            setTimeout(function() {
                swal.close()
                if (data.status) {
                    swal.fire({
                        "title": "Sukses",
                        "text": "Data Berhasil " + msg,
                        "icon": "success"
                    }).then((data) => {
                        if (data.value) {
                            window.location.href = base+'profile';
                        }
                    });
                }else{
                    $.each(data.errors, function(key, value) {
                        $('[name="' + key + '"]').addClass('is-invalid');
                        $('[name="' + key + '"]').nextAll('.invalid-feedback').text(value); 
                        if (value == "") {
                            $('[name="' + key + '"]').removeClass('is-invalid');
                            $('[name="' + key + '"]').addClass('is-valid');
                        }
                    });
                }
            }, 800);
        },
        error: function(data) {
            console.log(data.responseText);
            // toastr["error"]('Terjadi Error Pada Data');
            // $('#modal').modal('hide');
        }
    })
});
$('form#profileForm input').on('keydown', function(){
    console.log('open submit');
    $('#saveBtn').prop('disabled', false);
});
$('form#profileForm input').on('change', function(){
    $(this).removeClass('is-invalid');  
    $(this).nextAll('.invalid-feedback').text(''); 
});
$('#saveBtn').on('click', function(){
    $('#profileForm').trigger('submit');
});

$('form#uploadForm input').on('change', function(){
    $('#uploadBtn').prop('disabled', false);
    console.log('foto udah tukar');
});
$('#uploadBtn').on('click', function(){
    $('#uploadForm').trigger('submit');
});

$('#uploadForm').submit(function(e){
    $(this).find('input[name="'+csrfname+'"]').val($.cookie('csrf_cookie_name'));
    e.preventDefault();
    $.ajax({
        url: $(this).attr('action'),
        data: new FormData(this),
        type: 'post', dataType: 'json',
        processData: false,cache: false,
        contentType: false,async: false,
        beforeSend: function() {
            swal.fire({title: 'Menunggu',html: 'Memproses data',didOpen: () => {swal.showLoading()}})
        },
        success: function(data) {
            console.log(data);
            setTimeout(function() {
                swal.close()
                if (data.status) {
                    swal.fire({
                        "title": "Sukses","icon": "success",
                        "text": "Data Berhasil merubah foto",
                    }).then((data) => {
                        if (data.value) {
                            window.location.href = base+'profile';
                        }
                    });
                }else{
                    console.log('gagal upload');
                }
            }, 800);
        },
        error: function(data) {
            console.log(data.responseText);
            // toastr["error"]('Terjadi Error Pada Data');
            // $('#modal').modal('hide');
        }
    })
});

$('#passForm').submit(function(e){
    $(this).find('input[name="'+csrfname+'"]').val($.cookie('csrf_cookie_name'));
    e.preventDefault();
    $.ajax({
        url: $(this).attr('action'),
        data: new FormData(this),
        type: 'post', dataType: 'json',
        processData: false,
        contentType: false,
        cache: false,
        async: false,
        beforeSend: function() {
            swal.fire({title: 'Menunggu',html: 'Memproses data',didOpen: () => {swal.showLoading()}})
        },
        success: function(data) {
            console.log(data);
            setTimeout(function() {
                swal.close()
                if (data.status) {
                    swal.fire({
                        "title": "Sukses",
                        "text": "Berhasil Merubah Password",
                        "icon": "success"
                    }).then((data) => {
                        if (data.value) {
                            window.location.href = base+'profile';
                        }
                    });
                }else{
                    $.each(data.errors, function(key, value) {
                        $('[name="' + key + '"]').addClass('is-invalid');
                        $('[name="' + key + '"]').nextAll('.invalid-feedback').text(value); 
                        if (value == "") {
                            $('[name="' + key + '"]').removeClass('is-invalid');
                            $('[name="' + key + '"]').addClass('is-valid');
                        }
                    });
                }
            }, 800);
        },
        error: function(data) {
            console.log(data.responseText);
            // toastr["error"]('Terjadi Error Pada Data');
            // $('#modal').modal('hide');
        }
    })
});