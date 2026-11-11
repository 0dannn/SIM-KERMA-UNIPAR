$(document).ready(function(){
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
    kolom = [
        {
            data: "id",
            orderable: false,
            searchable: false,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }
        },
        { data: "judul" },
        { data: "nm_unit" },
        { data: "tanggal" },
    ];
    kolom_def = [
        {
            targets: 4,
            data: "dokumen",
            render: function(data){
                button = ' - ';
                if(data != ''){
                    button = `<a class="btn btn-sm btn-success download-dokumen" href="${site}/uploads/penerapan/${data}">
                    <i class="fa fa-file-download"></i> Download</a>`;
                }
                return button;
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
    default_table_init('penerapan', 'penerapan/data_penerapan/'+id_unit, kolom, kolom_def);
});
var myModalEl = document.getElementById('myModal')
myModalEl.addEventListener('hide.bs.modal', function (event) {
    $('#penerapanForm')[0].reset();
    $('#penerapanForm input').trigger('change');
    $(".select2").val('').trigger('change');
    $(".dropify").val('').trigger('change');
})
const myModal = new bootstrap.Modal(document.getElementById('myModal'))
$('#tambahBtn').on('click', function(){
    init_dropify('#dokumen');
    $('#penerapanForm [name="mode"]').val('add');
    myModal.show();
});

$('form#penerapanForm input, form#penerapanForm select').on('change', function(){
    $(this).removeClass('is-invalid');  
    $(this).nextAll('.invalid-feedback').text(''); 
});

_submit('#penerapanForm', function(data, msg){
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
                    reload_ajax('penerapan')
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
    $('#penerapanForm').trigger('submit');
});
function hapus(id){
    _hapus(id, 'penerapan/hapusAjax',function(){
        setTimeout(function() {
            swal.close()
            swal.fire({
                "title": "Sukses",
                "text": "Data Berhasil dihapus",
                "icon": "success"
            }).then((data) => {
                if (data.value) {
                    reload_ajax('penerapan');
                }
            });
        }, 800);
    })
}
function edit(id){
    _edit(id, 'penerapan/editAjax', function(data){
        console.log(data);
        penerapan = data.data;
        $('#penerapanForm [name="mode"]').val('edit');
        $('#penerapanForm [name="id"]').val(penerapan.id)
        $('#penerapanForm [name="judul"]').val(penerapan.judul)
        $('#penerapanForm [name="unit_id"]').val(penerapan.unit_id).trigger('change');
        $('#penerapanForm [name="tanggal"]').val(penerapan.tanggal)
        
        link = penerapan.dokumen != '' ? base + 'uploads/penerapan/' +penerapan.dokumen : '';
        init_dropify('#dokumen', link);

        setTimeout(function() { 
            swal.close()
            myModal.show();
        }, 800);
    })
}