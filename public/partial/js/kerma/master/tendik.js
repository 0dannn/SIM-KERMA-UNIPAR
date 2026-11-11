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
        { data: "nama" },
        { data: "email" },
        { data: "nm_unit" },
    ];
    kolom_def = [
        {
            targets: 4,
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
    default_table_init('tendik', 'master/get_data_tendik', kolom, kolom_def);
});
var myModalEl = document.getElementById('myModal')
myModalEl.addEventListener('hide.bs.modal', function (event) {
    $('#tendikForm')[0].reset();
    $('#tendikForm input').trigger('change');
    $(".select2").val('').trigger('change');
})
const myModal = new bootstrap.Modal(document.getElementById('myModal'))
$('#tambahBtn').on('click', function(){
    $('#tendikForm [name="mode"]').val('add');
    myModal.show();
});

$('form#tendikForm input, form#tendikForm select').on('change', function(){
    $(this).removeClass('is-valid');
    $(this).removeClass('is-invalid');
    $(this).nextAll('.invalid-feedback').text(''); 
});

_submit('#tendikForm', function(data, msg){
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
                    reload_ajax('tendik')
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
    $('#tendikForm').trigger('submit');
});

function edit(id){
    _edit(id, 'master/editTendik', function(data){
        console.log(data);
        penerapan = data.data;
        $('#tendikForm [name="mode"]').val('edit');
        $('#tendikForm [name="id"]').val(penerapan.id)
        $('#tendikForm [name="nama"]').val(penerapan.nama)
        $('#tendikForm [name="email"]').val(penerapan.email)
        $('#tendikForm [name="unit_id"]').val(penerapan.unit_id).trigger('change');
        setTimeout(function() { 
            swal.close()
            myModal.show();
        }, 800);
    })
}

function hapus(id){
    _hapus(id, 'master/hapusTendik',function(){
        setTimeout(function() {
            swal.close()
            swal.fire({
                "title": "Sukses",
                "text": "Data Berhasil dihapus",
                "icon": "success"
            }).then((data) => {
                if (data.value) {
                    reload_ajax('tendik');
                }
            });
        }, 800);
    })
}