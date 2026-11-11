$(document).ready(function(){
    kolom_unit = [
        {
            data: "id",
            orderable: false,
            searchable: false,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }
        },
        { data: "nm_unit" },
    ];
    defkolom_unit = [
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
    default_table_init('unit', 'master/data_unit', kolom_unit, defkolom_unit);
});
$('#saveBtn').on('click', function(){
    $('#unitForm').trigger('submit');
});
_submit('#unitForm', function(data, msg){
    setTimeout(function() {
        swal.close()
        if (data.status) {
            swal.fire({
                "title": "Sukses",
                "text": "Data Berhasil " + msg,
                "icon": "success"
            }).then((data) => {
                if (data.value) {
                    crud_toggle()
                    reload_ajax('unit');
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
    var id = $('#unitForm').find('input[name="id"]');
    var nama = $('#unitForm').find('input[name="nm_unit"]');
    var mode = $('#unitForm').find('input[name="mode"]');
    if(batal){
        $('#unitTitle').text('Tambah Data')
        $('#batalBtn').addClass('invisible').prop('disabled', true);
        id.val('');
        mode.val('add')
        nama.val('').removeClass('is-valid');
    }else{
        $('#unitTitle').text('Edit Data')
        $('#batalBtn').removeClass('invisible').prop('disabled', false);
        id.val(value.id);
        nama.val(value.nm_unit).addClass('is-valid').trigger('change');
        mode.val('edit')
    }
}
$('#batalBtn').on('click', function(){
    crud_toggle()
})

$('form#unitForm input').on('change', function(){
    $(this).removeClass('is-invalid');  
    $(this).nextAll('.invalid-feedback').text(''); 
});

function edit(id)
{
    _edit(id, 'master/edit_unit', function(data){
        setTimeout(function() {
            swal.close()
            crud_toggle(false, data.data)
        }, 800);
    })
}
function hapus(id){
    _hapus(id, 'master/delete_unit',function(data){
        setTimeout(function() {
            swal.close()
            swal.fire({
                "title": "Sukses",
                "text": "Data Berhasil dihapus",
                "icon": "success"
            }).then((data) => {
                if (data.value) {
                    reload_ajax('unit')
                }
            });
        }, 800);
    })
}
var sinkron_table;
const myModal = new bootstrap.Modal(document.getElementById('myModal'));
function import_siakad(){
    ajaxcsrf();
    $.ajax({
        url:site+'master/data_prodi',
        method: 'POST', dataType:'JSON',
        error: function(data)
        {
            console.log(data.responseText);
            ask_to_reload()
        }
    }).then(function(result){
        var unit = result.prodi;
        var columns= [
            {
                data: "id",
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: "nm_lemb" },
            { 
                data: "id_jenj_didik",
                render: function(data){
                    var jenj;
                    if(data == 22){
                        jenj = 'D3';
                    }else if(data == 30){
                        jenj = 'S1';
                    }else if(data == 31){
                        jenj = 'S1';
                    }
                    return jenj
                }
             },
            { data: "id_sms"},
        ];
        var defkolom = [
            {
                targets: 3,
                data: "id_sms",
                render: function(data, type, row, meta) {
                    val = `<button class="btn btn-primary single-import" data-id="${data}"> <i class="fa fa-plus"></i></button>`;
                    unit.forEach(function(item, index){
                        if(data == item.id_sms){
                            val = '<button class="btn btn-danger" disabled> <i class="fa fa-times"></i></button>';
                            // unit.push({'id_sms':item.id_sms});
                        }
                    })
                    return val;
                }
            }
        ];
        // ======= Deklarasikan Table yang ada di modal ===========
        sinkron_table  = default_table_init('sinkron', 'master/data_siakad', columns, defkolom);
        // ======= Open The Modal ====== //
        myModal.show();
        $('#sinkron').on('click', '.single-import', function(){
            var button = $(this);
            var old_button = button.html();
            id = $(this).data('id');
            ajaxcsrf();
            $.ajax({
                url: site+'master/ajaxImport',
                method: 'POST', dataType: 'JSON',
                data: {id:id},
                beforeSend: function(){
                    button.html('<i class="fa fa-spinner fa-spin"></i>');
                },
                success: function(data){
                    setTimeout(function() {
                        button.html(old_button)
                        unit.push({'id_sms':id});
                        reload_ajax('sinkron');
                        reload_ajax('unit');
                    }, 800);
                },
                error: function(data)
                {
                    console.log(data.responseText);
                    ask_to_reload()
                }
            });
        });
    });
}