// menampilkan tanggapan dengan simple datatables
// load data table komen
columns = [{
    data: "id",
    orderable: false,
    searchable: false,
    render: function(data, type, row, meta) {
        nama = row.first_name != null ? row.first_name + ' ' + row.last_name : row.nama
        gambar = row.img_name == null ? 'default.png' : row.img_name
        return `<div class="d-flex align-items-center">
                <div class="avatar-md flex-shrink-0 me-3">
                    <img src="${base+'/partial/images/users/'+gambar}" alt="" class="img-thumbnail rounded-circle">
                </div>
                <div class="flex-grow-1">
                    <div>
                        <b class="font-size-14 mb-1">${nama}</b><span class="font-size-12 text-muted float-end"> ${date_show2(row.create_at)}</span>
                        <p class="font-size-13 text-muted mb-0">${row.email}</p>
                    </div>
                </div>
            </div>`;
    }
}, ];
columnDefs = [];

$(document).ready(function(){
    $('.ui-sortable').sortable({
        placeholder: "panel-placeholder",
        start: function(event, ui){
            var classes = ui.item.attr('class').split(/\s+/);
            for(var x=0; x<classes.length;x++){   
                if (classes[x].indexOf("col")>-1){
                ui.placeholder.addClass(classes[x]);
                }
            }
            
            ui.placeholder.css({      
                width: ui.item.innerWidth() - 30 + 1,
                height: ui.item.innerHeight() - 15 + 1,     
                padding: ui.item.css("padding"),
                marginTop: 0
            });       
        },
         update: function() {
            var arr = $('input[name="qid[]"]').map(function () {
                return this.value; // $(this).val()
            }).get();

            ajaxcsrf();
            $.ajax({
                url: site+"survey/update_qsort",
                method:'POST',dataType:'JSON',
                data: {qid:arr},
                beforeSend: function() {
                    alertify.success('Menyimpan Urutan Pertanyaan.');
                },
                success:function(data){
                    console.log(data);
                    setTimeout(function() {
                        if(data.status){
                            alertify.success('Berhasil menyimpan Urutan Pertanyaan.');
                        }else{
                            alertify.error('Gagal menyimpan Urutan Pertanyaan.');
                        }
                    }, 800);
                }
            });
        }
    })

    // ambil id surat dari form untuk menampilkan komen berdasarkan suratnya
    token = $('#table_tanggapan').data('token')
    console.log('token adalah = ' + token);
    simple_datatables('table_tanggapan', 'survey/data_tanggapan/' + token, columns, columnDefs);

});

$('.panel').on('mousedown', function(){
	$(this).css( 'cursor', 'move' );
}).on('mouseup', function(){
	$(this).css( 'cursor', 'auto' );
});;

var myModalEl = document.getElementById('myModal')
myModalEl.addEventListener('hide.bs.modal', function (event) {
    $('#manageForm')[0].reset();
    empty();
})
const myModal = new bootstrap.Modal(document.getElementById('myModal'))
$('#tambahBtn').on('click', function(){
    $('#manageForm [name="mode"]').val('add');
    myModal.show();
});

function new_check(_this) {
    var tbody = _this.closest('.row').siblings('table').find('tbody')
    var count = tbody.find('tr').last().find('.icheck-primary').attr('data-count')
    count++;
    console.log(count)
    var opt = '';
    opt += '<td class="text-center pt-1"><div class="icheck-primary d-inline" data-count = "' + count + '"><input type="checkbox" id="checkboxPrimary' + count + '"><label for="checkboxPrimary' + count + '"> </label></div></td>';
    opt += '<td class="text-center"><input type="text" class="form-control form-control-sm check_inp" name="label[]"></td>';
    opt += '<td class="text-center"><a href="javascript:void(0)" onclick="$(this).closest(\'tr\').remove()"><span class="fa fa-times" ></span></a></td>';
    var tr = $('<tr></tr>')
    tr.append(opt)
    tbody.append(tr)
}

function new_radio(_this, edit = false, data) {
    var tbody = _this.closest('.row').siblings('table').find('tbody')
    var count = tbody.find('tr').last().find('.icheck-primary').attr('data-count')

    var opt = '';
    opt += '<td class="text-center pt-1"><div class="icheck-primary d-inline" data-count = "' + count + '"><input type="radio" id="radioPrimary' + count + '" name="radio"><label for="radioPrimary' + count + '"> </label></div></td>';
    opt += '<td class="text-center"><input type="text" class="form-control form-control-sm check_inp" name="label[]"></td>';
    opt += '<td class="text-center"><a href="javascript:void(0)" onclick="$(this).closest(\'tr\').remove()"><span class="fa fa-times" ></span></a></td>';
    var tr = $('<tr></tr>')
    tr.append(opt)
    tbody.append(tr)
}

function new_option(type, value)
{
    var tbody = $('#opt_table').find('tbody');
    element = `<td class="text-center">
                    <div class="icheck-primary d-inline" data-count='1'>
                        <input type="${type}" id="checkboxPrimary1" checked="">
                        <label for="checkboxPrimary1">
                        </label>
                    </div>
                </td>

                <td class="text-center">
                    <input type="text" class="form-control form-control-sm check_inp" name="label[]" value="${value}">
                </td>
                <td class="text-center"></td>`;
    var tr = $('<tr></tr>')
    tr.append(element)
    tbody.append(tr)
}

function check_opt() {
    var check_opt_clone = $('#check_opt_clone').clone()
    $('.preview').html(check_opt_clone.html())
}

function radio_opt() {
    var radio_opt_clone = $('#radio_opt_clone').clone()
    $('.preview').html(radio_opt_clone.html())
}

function textfield_s() {
    var textfield_s_clone = $('#textfield_s_clone').clone()
    $('.preview').html(textfield_s_clone.html())
}
function empty(){
    em = ` <p class="float-center">
    <i>Untuk melihat preview, Silahkan dipilih Tipe pertanyaan terlebih dahulu.</i>
    </p>`;
    $('.preview').html(em);
}
function level_s(){
    var level_s_clone = $('#level_s_clone').clone()
    $('.preview').html(level_s_clone.html())
}
$('[name="type"]').change(function() {
    var tipe = $(this).val();
    console.log(tipe);
    if(tipe != 'desc'){
        window[$(this).val()]()
    }else{
        empty()
    }
})

_submit('#manageForm', function(data, msg){
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
                    window.location.reload();
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
    $('#manageForm').trigger('submit');
});

$('.edit_pertanyaan').on('click', function(){
    var id = $(this).data('id');
    _edit(id, 'survey/editPertanyaan', function(data){
        console.log(data);
        $('#manageForm [name="mode"]').val('edit');
        $('#manageForm [name="id"]').val(data.id)
        $('#manageForm [name="pertanyaan"]').val(data.pertanyaan)
        $('#manageForm [name="type"]').val(data.type);
        var opt = '';
        if(data.type == 'radio_opt' && data.type == 'check_opt'){
            opt = data.type == 'radio_opt' ? 'radio' : 'checkbox';
            option = JSON.parse(data.frm_option)
            count_o = Object.keys(option).length;
            var opt_clone = $('#opt_clone').clone()
            $('.preview').html(opt_clone.html());
            Object.keys(option).forEach(function(key, count){
                new_option(opt, option[key]);
            });
        }else if(data.type == 'textfield_s'){
            textfield_s();
        }else if(data.type == 'level_s'){
            level_s();
        }else{
            empty()
        }
        setTimeout(function() { 
            swal.close()
            myModal.show();
        }, 800);
    })
});
$('.hapus_pertanyaan').on('click', function(){
    var id = $(this).data('id')
    _hapus(id, 'survey/hapusPertanyaan',function(){
        setTimeout(function() {
            swal.close()
            swal.fire({
                "title": "Sukses",
                "text": "Data Berhasil dihapus",
                "icon": "success"
            }).then((data) => {
                if (data.value) {
                    window.location.reload();
                }
            });
        }, 800);
    });
});

$('.statuskue').on('click', function(){
    id = $(this).data('id');
    mode = $(this).data('mode');
    Swal.fire({
        title: "Anda yakin?",
        text: "ingin " +mode+" kuesioner ini?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya!",
        cancelButtonText: "Batal"
    }).then(result => {
        if (result.value) {
            ajaxcsrf();
            $.ajax({
                url: site+'survey/ajax_publis',
                data : {id:id, mode:mode},method:'POST',dataType:'JSON',
                beforeSend: function() {
                    swal.fire({title: 'Menunggu',html: 'Memproses data',didOpen: () => {swal.showLoading()}})
                },
                success: function(resp){
                    console.log(resp);
                    if(resp.status){
                        setTimeout(function() {
                            swal.fire({
                                "title": "Sukses",
                                "text": "Berhasil Merubah Mode Kuesioner Menjadi " + mode,
                                "icon": "success"
                            }).then((data) => {
                                if (data.value) {
                                    window.location.reload();
                                }
                            });
                        }, 800);
                    }
                },
                error: function(data)
                {
                    console.log(data.responseText);
                    ask_to_reload()
                }
            });
        }
    });
});

