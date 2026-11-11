var table;
function reload_ajax() {
    table.ajax.reload(null, false);
}
$(document).ready(function() {
    table = $("#group").DataTable({
        initComplete: function() {
            var api = this.api();
            $("#group_filter input")
                .off(".DT")
                .on("keyup.DT", function(e) {
                    api.search(this.value).draw();
                });
        },
        dom:
            "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        oLanguage: {
            sProcessing: "loading..."
        },
        processing: true,
        serverSide: true,
        ajax: {
            url: site + "users/data_group",
            type: "POST",
            data: function(data){
                var value = $.cookie('csrf_cookie_name');
                data[csrfname] = value
            },
        },
        columns: [
            {
                data: "id",
                orderable: false,
                searchable: false
            },
            { data: "name" },
            { data: "description" }
        ],
        columnDefs: [
            {
                targets: 3,
                data: "id",
                render: function(data, type, row, meta) {
                    return `<div class="text-center">
                            <button class="btn btn-xs btn-warning" onclick="edit(${data})">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-xs btn-danger" onclick="hapus(${data})">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>`;
                }
            }
        ],
        order: [[1, "asc"]],
        rowId: function(a) {
            return a;
        },
        rowCallback: function(row, data, iDisplayIndex) {
            var info = this.fnPagingInfo();
            var page = info.iPage;
            var length = info.iLength;
            var index = page * length + (iDisplayIndex + 1);
            $("td:eq(0)", row).html(index);
        }
    });
});

$.fn.dataTableExt.oApi.fnPagingInfo = function(oSettings) {
    return {
        "iStart": oSettings._iDisplayStart,
        "iEnd": oSettings.fnDisplayEnd(),
        "iLength": oSettings._iDisplayLength,
        "iTotal": oSettings.fnRecordsTotal(),
        "iFilteredTotal": oSettings.fnRecordsDisplay(),
        "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
        "iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
    };
};

var myModalEl = document.getElementById('myModal')
myModalEl.addEventListener('hide.bs.modal', function (event) {
    $('#groupForm')[0].reset();
})
const myModal = new bootstrap.Modal(document.getElementById('myModal'))
$('#tambahGroup').on('click', function(){
    myModal.show();
});

$('#groupForm').submit(function(e){
    var mode = $(this).find('input[name="mode"]').val();
    var msg = mode == 'add' ? 'disimpan' : 'diedit';
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
                            reload_ajax()
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
        error: function(data) {
            console.log(data.responseText);
            // toastr["error"]('Terjadi Error Pada Data');
            // $('#modal').modal('hide');
        }
    })
});
$('form#groupForm input, form#groupForm select').on('change', function(){
    $(this).removeClass('is-invalid');  
    $(this).nextAll('.invalid-feedback').text(''); 
});
$('#saveBtn').on('click', function(){
    $('#groupForm').trigger('submit');
});

function edit(id){
    ajaxcsrf();
    $.ajax({
        url: site+'users/edit_group',
        method:'POST',dataType:'JSON',
        data: {id:id},
        beforeSend: function(){
            swal.fire({title: 'Menunggu',html: 'Memproses data',didOpen: () => {swal.showLoading()}})
        },
        success: function(data){
            if(data.status){
                $('#groupForm input[name="mode"]').val('edit');
                $('#groupForm').append('<input type="hidden" name="id" value="'+ data.result.id +'"/>');
                $('#groupForm input[name="name"]').val(data.result.name);
                $('#groupForm input[name="description"]').val(data.result.description);
                setTimeout(function() {
                    swal.close();
                    myModal.show();
                }, 800);
            }
        }
    });
}
function hapus(id) {
    ajaxcsrf();
    Swal.fire({
        title: "Anda yakin?",
        text: "Data akan dihapus.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Hapus!"
    }).then(result => {
        if (result.value) {
            $.getJSON(site + "users/delete_group/" + id, function(data) {
                Swal.fire({
                    title: data.status ? "Berhasil" : "Gagal",
                    text: data.status
                        ? "User berhasil dihapus"
                        : "User gagal dihapus",
                    icon: data.status ? "success" : "error"
                });
                reload_ajax();
            });
        }
    });
}