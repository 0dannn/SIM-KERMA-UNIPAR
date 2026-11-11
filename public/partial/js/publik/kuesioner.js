console.log('kuesioner');
_submit('#pesertaForm', function(data, msg){
    setTimeout(function() {
        swal.close()
        if (data.status) {
            location.reload();
        }else{
            if(data.errors != undefined){
                error_display(data.errors);
            }
            if(data.msg != undefined){
                swal.fire({
                    "title": "Gagal",
                    "text": data.msg,
                    "icon": "error"
                })
            }
        }
    }, 800);
});

_submit('#formTanggapan', function(data, msg){
    setTimeout(function() {
        swal.close()
        if (data.status) {
            swal.fire({
                "title": "Sukses",
                "text": "Tanggapan Berhasil dikirim",
                "icon": "success",
                "allowOutsideClick": false
            }).then((data) => {
                if (data.value) {
                    location.reload();
                }
            });
        }else{
            if(data.errors != undefined){
                $.each(data.errors, function(key, value) {
                    kunci = key.replace(/[\[\]']+/g,'')
                    el = $('#'+ kunci)
                    el.addClass('is-invalid');
                    el.text(value);
                    if (value == "") {
                        el.removeClass('is-invalid');
                        el.addClass('is-valid');
                    }
                });
            }
        }
    }, 800);
});

function test(id)
{
    cek = $('#'+ id)
    console.log(cek);
}

$('#simpan').on('click', function(){
    Swal.fire({
        title: "Anda yakin?",
        text: "ingin mengirim tanggapan ini?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya!",
        cancelButtonText: "Batal"
    }).then(result => {
        if (result.value) {
            $('#formTanggapan').trigger('submit');
        }
    });
});

$('form#formTanggapan input, form#formTanggapan textarea').on('change', function(){
    element = $(this).closest('.rounded'); 
    element.find('.feedback').text()
});