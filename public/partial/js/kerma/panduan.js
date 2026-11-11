$(document).ready(function(){
    if (document.getElementById('panduan')) {
        columns = [
            {
                data: "id",
                orderable: false,
                searchable: false
            },
            { data: "judul" },
            { data: "deskripsi" },
            { data: "tanggal" },
            {
                data: "dokumen",
                orderable: false,
                searchable: false
            },
        ];
        columnDefs = [
            {
                targets: 4,
                data: "dokumen",
                render: function(data){
                    return `<button type="button" class="btn btn-sm btn-success downBtn" onclick="down_file('${data}')"><i class="fa fa-download"></i> 
                        Download</button>`;
                }
            },
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
        default_table_init('panduan', 'panduan/data', columns, columnDefs);
    }
});
$('#reloadBtn').on('click', function(){
    var active = $('.nav-link.active').data('type');
    if(active == 'table'){
        $('#panduan').DataTable().ajax.reload();
    }else if(active == 'grid'){
        window.location.href = base+'panduan/index/grid';
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
    $('#panduanForm')[0].reset();
    $('#panduanForm input').trigger('change');
    $(".dropify").val('').trigger('change');
})
const myModal = new bootstrap.Modal(document.getElementById('myModal'))
$('#tambahBtn').on('click', function(){
    init_dropify('#cover');
    init_dropify('#dokumen');
    $('#panduanForm [name="mode"]').val('add');
    myModal.show();
});

// declar submit function with ajax

_submit('#panduanForm', function(data, msg){
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
                        $('#panduan').DataTable().ajax.reload();
                    }else{
                        window.location.href = base+'panduan/index/grid';
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
    $('#panduanForm').trigger('submit');
});
function hapus(id){
    _hapus(id, 'panduan/hapusAjax',function(){
        setTimeout(function() {
            swal.close()
            swal.fire({
                "title": "Sukses",
                "text": "Data Berhasil dihapus",
                "icon": "success"
            }).then((data) => {
                if (data.value) {
                    reload_ajax('panduan')
                }
            });
        }, 800);
    })
}
function edit(id){
    _edit(id, 'panduan/editAjax', function(data){
        console.log(data);
        var panduan = data.data;
        $('#panduanForm [name="mode"]').val('edit');
        $('#panduanForm [name="id"]').val(panduan.id)
        $('#panduanForm [name="judul"]').val(panduan.judul)
        $('#panduanForm [name="deskripsi"]').val(panduan.deskripsi)
        $('#panduanForm [name="tanggal"]').val(panduan.tanggal);

        link_cover = panduan.cover != '' ? base + 'uploads/panduan/' +panduan.cover : '';
        init_dropify('#cover', link_cover);
        link_dokumen = panduan.dokumen != '' ? base + 'uploads/panduan/' +panduan.dokumen : '';
        init_dropify('#dokumen', link_dokumen);

        setTimeout(function() {
            swal.close()
            myModal.show();
        }, 800);
    })
}