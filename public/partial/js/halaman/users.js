var table;
$(document).ready(function() {
    table = $("#users").DataTable({
        initComplete: function() {
            var api = this.api();
            $("#users_filter input")
                .off(".DT")
                .on("keyup.DT", function(e) {
                    api.search(this.value).draw();
                });
        },
        dom:
            "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            {
                extend: "copy",
                exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7] }
            },
            {
                extend: "print",
                exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7] }
            },
            {
                extend: "excel",
                exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7] }
            },
            {
                extend: "pdf",
                exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7] }
            }
        ],
        oLanguage: {
            sProcessing: "loading..."
        },
        processing: true,
        serverSide: true,
        ajax: {
            url: site + "users/data/" + user_id,
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
            { data: "first_name" },
            { data: "last_name" },
            { data: "username" },
            { data: "email" },
            { 
                data: "level",
                orderable: false,
                searchable: false
            },
        ],
        columnDefs: [
            {
                targets: 5,
                data: "level",
                render: function(data, type, row, meta) {
                    const myArray = data.split(",");
                    var kolek = '<div class="text-center">';
                    for (let x in myArray) {
                        kolek += '<span class="badge bg-secondary">' + myArray[x] + "</span> ";
                    }
                    kolek += '</div>';
                    return kolek;
                }
            },
            {
                targets: 6,
                orderable: false,
                searchable: false,
                title: "Status",
                data: "active",
                render: function(data, type, row, meta) {
                    if (data === "1") {
                        return `<div class="text-center">
                                <span class="badge bg-success">Active</span>
                            </div>`;
                    } else {
                        return `<div class="text-center">
                                <span class="badge bg-danger">Not Active</span>
                            </div>`;
                    }
                }
            },
            {
                targets: 7,
                data: "id",
                render: function(data, type, row, meta) {
                    if (data === user_id) {
                        return `<div class="text-center">
                                <button class="btn btn-xs bg-primary editUsers" onclick="edit(${data})">
                                    <i class="fa fa-cog fa-spin"></i>
                                </button>
                            </div>`;
                    } else {
                        return `<div class="text-center">
                                <button class="btn btn-xs btn-warning editUsers" onclick="edit(${data})">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-danger" onclick="hapus(${data})">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>`;
                    }
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

    table
        .buttons()
        .container()
        .appendTo("#users_wrapper .col-md-6:eq(0)");

    $("#show_me").on("change", function() {
        let src = site + "users/data";
        let url = $(this).prop("checked") === true ? src : src + "/" + user_id;
        table.ajax.url(url).load();
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
    $('.editUsers').on('click', function(){
        console.log('edit');
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

function reload_ajax() {
    table.ajax.reload(null, false);
}
var myModalEl = document.getElementById('myModal')
myModalEl.addEventListener('hide.bs.modal', function (event) {
    // do something...
    $('#userForm')[0].reset();
    // $('#userForm').trigger('change');
    $(".select2").val('').trigger('change');
})
const myModal = new bootstrap.Modal(document.getElementById('myModal'))
$('#tambahUser').on('click', function(){
    myModal.show();
});

$('form#userForm input, form#userForm select').on('change', function(){
    $(this).removeClass('is-invalid');  
    $(this).nextAll('.invalid-feedback').text(''); 
});
$('#userForm').submit(function(e){
    var mode = $(this).find('input[name="mode"]').val();
    var msg = mode == 'add' ? 'disimpan' : 'diedit';
    $(this).find('input[name="'+csrfname+'"]').val($.cookie('csrf_cookie_name'));
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
                            // window.location.href = base+'users';
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
        }
    })
});

$('#saveBtn').on('click', function(){
    $('#userForm').trigger('submit');
});

function edit(id){
    ajaxcsrf();
    $.ajax({
        url: site+'users/edit',
        method:'POST',dataType:'JSON',
        data: {id:id},
        beforeSend: function(){
            swal.fire({title: 'Menunggu',html: 'Memproses data',didOpen: () => {swal.showLoading()}})
        },
        success: function(data){
            if(data.status){
                fullname = data.result.first_name + ' ' + data.result.last_name
                $('#userForm input[name="mode"]').val('edit');
                $('#userForm').append('<input type="hidden" name="id" value="'+ data.result.id +'"/>');
                $('#userForm input[name="full_name"]').val(fullname);
                $('#userForm input[name="username"]').val(data.result.username);
                $('#userForm input[name="email"]').val(data.result.email);
                level = data.result.level.split(":");
                if(data.result.active == '0'){
                    $('#user_deactive').prop("checked", true);
                }else if(data.result.active == '1'){
                    $('#user_active').prop("checked", true);
                }
                $('#level').val(level).trigger('change');
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
            $.getJSON(site + "users/delete/" + id, function(data) {
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
