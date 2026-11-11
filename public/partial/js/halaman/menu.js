$(document).ready(function(){
    $('#nestable').nestable({
        group: 1
    });
    $('.select2').each(function(index, element) {
        var initValue = element.dataset.value != undefined ? element.dataset.value : false;
        var item = $(element);
        if (item.data('url')) {
            CustomInitSelect2(item, {
                url: item.data('url'),
                initialValue: initValue,
            }, myModalEl);
        } else {
            item.select2();
        }
    });
});

var myModalEl = document.getElementById('myModal')
myModalEl.addEventListener('hide.bs.modal', function (event) {
    $('#menuForm')[0].reset();
    $(".select2").val('').trigger('change') ;
})

const myModal = new bootstrap.Modal(document.getElementById('myModal'))
$('#tambah-menu').on('click', function(){
    myModal.show();
});

$('.addchildBtn').on('click', function(){
    var id = $(this).data('id')
    $('#parent').val(id).trigger('change');
    myModal.show();
});
    
$('form#menuForm input, form#menuForm select').on('change', function(){
    $(this).removeClass('is-invalid');  
    $(this).nextAll('.invalid-feedback').text(''); 
});
$('#menuForm').submit(function(e){
    var mode = $(this).find('input[name="mode"]').val();
    var msg = mode == 'add' ? 'disimpan' : 'diedit';
    $(this).find('input[name="'+csrfname+'"]').val($.cookie('csrf_cookie_name') );
    e.preventDefault();
    $.ajax({
        url: $(this).attr('action'),
        data : $(this).serialize(),
        type: 'post', dataType: 'json',
        processData: false,
        cache: false,
        async: false,
        beforeSend: function() {
            swal.fire({title: 'Menunggu',html: 'Memproses data',didOpen: () => {swal.showLoading()}})
        },
        success: function(data) {
            setTimeout(function() {
                swal.close()
                if (data.status) {
                    myModal.hide();
                    swal.fire({
                        "title": "Sukses",
                        "text": "Data Berhasil " + msg,
                        "icon": "success"
                    }).then((data) => {
                        if (data.value) {
                            window.location.href = base+'menu';
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
        error: function(xhr, textStatus, error) {
            swal.close()
            console.log(xhr.statusText);
            console.log(textStatus);
            console.log(error);
        },
    })
});

$('#saveBtn').on('click', function(){
    $('#menuForm').trigger('submit');
});

$('.editMenu').on('click', function(){
    id = $(this).data('id');
    ajaxcsrf();
    $.ajax({
        url:site+'menu/editMenu',
        method:'POST',dataType:'JSON',
        data: {id:id},
        beforeSend: function(){
            swal.fire({title: 'Menunggu',html: 'Memproses data',didOpen: () => {swal.showLoading()}})
        }
    }).then(function(data){
        $('#menuForm input[name="mode"]').val('edit');
        // append input for id
        $('#menuForm').append('<input type="hidden" name="id" value="'+ data.menu.id +'"/>');
        $('#menuForm input[name="icon"]').val(data.menu.icon).trigger('change');
        $('#menuForm input[name="label"]').val(data.menu.label).trigger('change');
        $('#menuForm input[name="link"]').val(data.menu.link).trigger('change');
        $('#parent').val(data.menu.parent).trigger('change');
        level = data.menu.level.split(":");
        $('#group').val(level).trigger('change');
        setTimeout(function() {
            swal.close()
            myModal.show();
        }, 800);
        console.log(data);
    });
});

$('.removeMenu').click(function() {
    id = $(this).data('id');
    ajaxcsrf();
    Swal.fire({
        title: "Apakah Kamu Yakin?",
        text: "Kamu tidak bisa mengembalikan nya lagi!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2ab57d",
        cancelButtonColor: "#fd625e",
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal"
      }).then(function (result) {
        if (result.value) {
            // send ajax to delete the data
            $.ajax({
                url: site+'menu/delete',
                method: 'post', dataType:'json',
                data:{id:id},
                beforeSend: function(){
                    swal.fire({title: 'Menunggu',html: 'Memproses data',didOpen: () => {swal.showLoading()}})
                },
                success: function(data){
                    setTimeout(function() {
                        if(data.status){
                            Swal.fire("Terhapus!", "Menu Berhasil dihapus.", "success").then((data) => {
                                if (data.value) {
                                    window.location.href = base+'menu';
                                }
                            });;
                        }
                    }, 800);
                }
            });
        }
    });
    return false;
}); /*end remove data click*/

$('#reloadBtn').on('click', function(){
    window.location.href = base+'menu';
})

$('.dd').on('change', function() {
    console.log('menu order change');
});
