$(document).ready(function(){
    if (document.getElementById('kegiatan')) {
        columns = [
            {
                data: "id",
                orderable: false,
                searchable: false
            },
            { data: "judul" },
            { data: "deskripsi" },
            { data: "full_name" },
            { data: "tanggal" },
        ];
        columnDefs = [
            {
                targets: 5,
                data: "id",
                render: function(data, type, row, meta) {
                    return `<div class="text-center">
                            <button class="btn btn-sm btn-warning editUsers" onclick="edit(${data})">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="hapus(${data})">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>`;
                }
            }
        ];
        default_table_init('kegiatan', 'kegiatan/data/'+ id_user, columns, columnDefs);
    }
});
$('#reloadBtn').on('click', function(){
    var active = $('.nav-link.active').data('type');
    if(active == 'table'){
        reload_ajax('kegiatan')
    }else if(active == 'grid'){
        window.location.href = base+'kegiatan/index/grid';
    }
})

function down_file(name){
    Swal.fire({
        title: "Anda yakin?",
        text: "ingin mendownload file ini?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya!",
        cancelButtonText: "Batal"
    }).then(result => {
        if (result.value) {
            console.log('name = ' + name);
        }
    });
}

var myModalEl = document.getElementById('myModal')
myModalEl.addEventListener('hide.bs.modal', function (event) {
    $('#kegiatanForm')[0].reset();
    $('#kegiatanForm input').trigger('change');
    $(".dropify").val('').trigger('change');
})
const myModal = new bootstrap.Modal(document.getElementById('myModal'))
$('#tambahBtn').on('click', function(){
    init_dropify('#cover');
    init_dropify('#dokumen');
    $('#kegiatanForm [name="mode"]').val('add');
    myModal.show();
});

// declar submit function with ajax

_submit('#kegiatanForm', function(data, msg){
    console.log(data);
    setTimeout(function() {
        swal.close()
        if (data.status) {
            myModal.hide()
            swal.fire({
                "title": "Sukses",
                "text": "Data Berhasil " + msg,
                "icon": "success"
            }).then((data) => {
                if (data.value) {
                    var active = $('.nav-link.active').data('type');
                    console.log(active);
                    if(active == 'table'){
                        reload_ajax('kegiatan')
                    }else{
                        window.location.href = base+'kegiatan/index/grid';
                    }
                }
            });
        }else{
            if(data.errors != undefined){
                $.each(data.errors, function(key, value) {
                    $('[name="' + key + '"]').addClass('is-invalid');
                    $('[name="' + key + '"]').nextAll('.invalid-feedback').text(value); 
                    if (value == "") {
                        $('[name="' + key + '"]').removeClass('is-invalid');
                        $('[name="' + key + '"]').addClass('is-valid');
                    }
                });
            }
        }
    }, 800);
});
$('#saveBtn').on('click', function(){
    $('#kegiatanForm').trigger('submit');
});
function hapus(id){
    _hapus(id, 'kegiatan/hapusAjax',function(){
        setTimeout(function() {
            swal.close()
            swal.fire({
                "title": "Sukses",
                "text": "Data Berhasil dihapus",
                "icon": "success"
            }).then((data) => {
                if (data.value) {
                    reload_ajax('kegiatan')
                }
            });
        }, 800);
    })
}
function edit(id){
    _edit(id, 'kegiatan/editAjax', function(data){
        console.log(data);
        var kegiatan = data.data;
        $('#kegiatanForm [name="mode"]').val('edit');
        $('#kegiatanForm [name="id"]').val(kegiatan.id)
        $('#kegiatanForm [name="judul"]').val(kegiatan.judul)
        $('#kegiatanForm [name="deskripsi"]').val(kegiatan.deskripsi)
        $('#kegiatanForm [name="tanggal"]').val(kegiatan.tanggal);

        link_cover = kegiatan.cover != '' ? base + 'uploads/kegiatan/' +kegiatan.cover : '';
        init_dropify('#cover', link_cover);

        setTimeout(function() {
            swal.close()
            myModal.show();
        }, 800);
    })
}