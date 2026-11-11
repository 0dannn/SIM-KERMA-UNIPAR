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
$('form#settingForm input').on('keydown', function(){
    $('#saveBtn').prop('disabled', false);
}); 

$('form#settingForm input').on('change', function(){
    $(this).removeClass('is-invalid');  
    $(this).nextAll('.invalid-feedback').text(''); 
});
$('#settingForm').submit(function(e){
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
                        "text": "Data Berhasil disimpan",
                        "icon": "success"
                    })
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
            swal.close()
            console.log(data.responseText);
        }
    })
});

$('form#uploadForm input').on('change', function(){
    $('#uploadBtn').prop('disabled', false);
    console.log('foto udah tukar');
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
                            window.location.href = base+'settings';
                        }
                    });
                }else{
                    swal.fire({
                        "title": "Gagal","icon": "error",
                        "html": data.pesan,
                    });
                    console.log('gagal upload');
                }
            }, 800);
        },
        error: function(data) {
            swal.close()
            console.log(data.responseText);
        }
    })
});