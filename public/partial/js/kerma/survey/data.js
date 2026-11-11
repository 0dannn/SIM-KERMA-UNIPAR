$(document).ready(function(){
    columns = [
        {
            data: "id",
            orderable: false,
            searchable: false
        },
        { data: "kode" },
        { data: "judul" },
        { data: "deskripsi" },
        { data: "tanggal_mulai" },
        { data: "tanggal_selesai" },
    ];
    columnDefs = [
        {
            targets: 6,
            data: "id",
            render: function(data, type, row, meta) {
                return `<div class="text-center">
                        <a href="${site}survey/detail/${data}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
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
    default_table_init('survey', 'survey/data/'+ user_id, columns, columnDefs);
});
var myModalEl = document.getElementById('myModal')
myModalEl.addEventListener('hide.bs.modal', function (event) {
    $('#surveyForm')[0].reset();
})
const myModal = new bootstrap.Modal(document.getElementById('myModal'))
$('#tambahBtn').on('click', function(){
    $('#surveyForm [name="mode"]').val('add');
    myModal.show();
});

$('form#surveyForm input, form#surveyForm textarea').on('change', function(){
    $(this).removeClass('is-invalid');  
    $(this).nextAll('.invalid-feedback').text(''); 
});

_submit('#surveyForm', function(data, msg){
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
                    reload_ajax('survey')
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
    $('#surveyForm').trigger('submit');
});

function edit(id){
    _edit(id, 'survey/editSurvey', function(data){
        console.log(data);
        $('#surveyForm [name="mode"]').val('edit');
        $('#surveyForm [name="id"]').val(data.id)
        $('#surveyForm [name="kode"]').val(data.kode)
        $('#surveyForm [name="judul"]').val(data.judul)
        $('#surveyForm [name="deskripsi"]').val(data.deskripsi)

        setTimeout(function() { 
            swal.close()
            myModal.show();
        }, 800);
    })
}

function hapus(id){
    _hapus(id, 'survey/hapusSurvey',function(){
        setTimeout(function() {
            swal.close()
            swal.fire({
                "title": "Sukses",
                "text": "Data Berhasil dihapus",
                "icon": "success"
            }).then((data) => {
                if (data.value) {
                    reload_ajax('survey');
                }
            });
        }, 800);
    })
}