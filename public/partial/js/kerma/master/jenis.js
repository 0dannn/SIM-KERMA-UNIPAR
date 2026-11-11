$(document).ready(function(){
    kolom = [
        {
            data: "id",
            orderable: false,
            searchable: false,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }
        },
        { data: "nama" },
    ];
    kolom_def = [
        {
            targets: 2,
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
    default_table_init('jenis', 'master/data_jenis_kerma', kolom, kolom_def);
});
$('#saveBtn').on('click', function(){
    $('#jenisForm').trigger('submit');
});
_submit('#jenisForm', function(data, msg){
    setTimeout(function() {
        swal.close()
        if (data.status) {
            swal.fire({
                "title": "Sukses",
                "text": "Data Berhasil " + msg,
                "icon": "success"
            }).then((data) => {
                if (data.value) {
                    crud_toggle();
                    reload_ajax('jenis');
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

function crud_toggle(batal = true, value = {})
{
    var id = $('#jenisForm').find('input[name="id"]');
    var nama = $('#jenisForm').find('input[name="nama"]');
    var mode = $('#jenisForm').find('input[name="mode"]');
    if(batal){
        $('#jenisTitle').text('Tambah Data')
        $('#batalBtn').addClass('invisible').prop('disabled', true);
        id.val('');
        mode.val('add')
        nama.val('').removeClass('is-valid');
    }else{
        $('#jenisTitle').text('Edit Data')
        $('#batalBtn').removeClass('invisible').prop('disabled', false);
        id.val(value.id);
        nama.val(value.nama).addClass('is-valid').trigger('change');
        mode.val('edit')
    }
}
$('#batalBtn').on('click', function(){
    crud_toggle()
})

$('form#jenisForm input').on('change', function(){
    $(this).removeClass('is-invalid');  
    $(this).nextAll('.invalid-feedback').text(''); 
});

function edit(id)
{
    _edit(id, 'master/edit_jenis', function(data){
        setTimeout(function() {
            swal.close()
            crud_toggle(false, data.data)
        }, 800);
    })
}
function hapus(id){
    _hapus(id, 'master/delete_jenis',function(data){
        setTimeout(function() {
            swal.close()
            swal.fire({
                "title": "Sukses",
                "text": "Data Berhasil dihapus",
                "icon": "success"
            }).then((data) => {
                if (data.value) {
                    reload_ajax('jenis');
                }
            });
        }, 800);
    })
}

/*
$('#level').val(level).trigger('change');

*/