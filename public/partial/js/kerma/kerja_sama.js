$(document).ready(function(){
    // deklarasi dua buah filter yang ada di halaman depan
    CustomInitSelect2($('#filter_unit'), {url: $('#filter_unit').data('url'),});
    CustomInitSelect2($('#filter_jenis'), {url: $('#filter_jenis').data('url'),});
    CustomInitSelect2($('#filter_tipe'), {url: $('#filter_tipe').data('url'),});
    CustomInitSelect2($('#filter_kategori'), {url: $('#filter_kategori').data('url'),});

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

    if ( ! $.fn.DataTable.isDataTable('#kerma') ) {
        // Setup datatables
        $.fn.dataTableExt.oApi.fnPagingInfo = function(oSettings)
        {
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
        $('body').tooltip({
            selector: '[data-toggle="tooltip"]'
        });

        $("#kerma").dataTable({
            initComplete: function() {
                var api = this.api();
                $('#mytable_filter input')
                    .off('.DT')
                    .on('input.DT', function() {
                        api.search(this.value).draw();
                });
            },
             // Tooltip
            oLanguage: {
                sProcessing: "loading..."
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: site + 'kerja_sama/data_kerma/'+ id_unit,
                type: "POST",
                data: function(data){
                    var value = $.cookie('csrf_cookie_name');
                    data[csrfname] = value
                    $('.select2-filter').each(function(index, element) {
                        item = $(element)
                        data[item.attr('name')] = item.val();  
                    })
                },
            },
            columns: [
                {
                    data: "id",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: "judul",
                    render: function(data, type, row) {
                        let displayText = data.length > 50 ? data.substr(0, 50) + "..." : data;
                        return `<span class="d-inline-block text-truncate" style="max-width: 200px;" data-toggle="tooltip" title="${data}">
                                    ${displayText}
                                </span>`;
                    }
                },
                {
                    data: "nm_unit", // Gabungan nm_unit dan mitra
                    render: function(data, type, row) {
                        return `<div>
                                    <strong>Unit:</strong> ${data}<br>
                                    <strong>Mitra:</strong> ${row.mitra}
                                </div>`;
                    }
                },
                {
                    data: "tanggal_mulai", // Gabungan tanggal_mulai dan tanggal_selesai
                    render: function(data, type, row) {
                        return `${data} s/d ${row.tanggal_selesai}`;
                    }
                },
                {
                    data: "create_at",
                    render: function(data) {
                        return data.split(" ")[0];
                    }
                },
                { data: "nama_tipe" },
                {
                    data: "status_kerma", // Gabungan tanggal_mulai dan tanggal_selesai
                    searchable: false,
                    render: function(data, type, row) {
                        return data == 1 ? "<span class='badge bg-primary'>Baru</span>" : "<span class='badge bg-secondary'>Perpanjangan</span>";
                    }
                },
            ],
            columnDefs: [
                {
                    targets: 7,
                    data: "dokumen",
                    render: function(data){
                        button = ' - ';
                        if(data != ''){
                            button = `<a class="btn btn-sm btn-success download-dokumen" href="${site}/uploads/kerma/${data}">
                    <i class="fa fa-file-download"></i> Download</a>`;
                        }
                        return button;
                    }
                },
                {
                    targets: 8,
                    data: "id",
                    render: function(data, type, row, meta) {
                        return `<div class="text-center">
                                <button class="btn btn-sm btn-info editUsers" onclick="edit(${data})">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="hapus(${data})">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>`;
                    }
                }
            ],
            createdRow: function( row, data, dataIndex ) {
                // Pengecualian: tipe IA tidak dihitung masa berlakunya
                if (data.nama_tipe === 'IA') {
                    return;
                }
                var selesai = new Date(data.tanggal_selesai);
                var today = new Date();
                var d = new Date(data.tanggal_selesai);
                d.setMonth(d.getMonth() - 5);
                var cek = '';
                if(today < selesai){
                    if(today >= d){
                        $(row).addClass('bg-warning');
                        cek = 'kuning';
                    }else if(today <= d){
                        cek = 'biru';
                    }
                }else{
                    $(row).addClass('bg-danger text-white');
                }
            },
            order: [[1, 'asc']],
            rowCallback: function(row, data, iDisplayIndex) {
                var info = this.fnPagingInfo();
                var page = info.iPage;
                var length = info.iLength;
                $('td:eq(0)', row).html();
            }
        });
    }else{
        $('#' + table_id).DataTable().ajax.reload();
    }
    var today = new Date();
    console.log(today);
});
// buat lakukan pengecekan apakah filter diklik
$('.select2-filter').on('change', function() {
    reload_ajax('kerma')
});

var myModalEl = document.getElementById('myModal')
myModalEl.addEventListener('hide.bs.modal', function (event) {
    $('#kermaForm')[0].reset();
    $('#kermaForm input').trigger('change');
    $(".select2").val('').trigger('change');
    $(".dropify").val('').trigger('change');
})
const myModal = new bootstrap.Modal(document.getElementById('myModal'))
$('#tambahBtn').on('click', function(){
    init_dropify('#dokumen');
    $('#kermaForm [name="mode"]').val('add');
    myModal.show();
});

$('form#kermaForm input, form#kermaForm select').on('change', function(){
    $(this).removeClass('is-invalid');  
    $(this).nextAll('.invalid-feedback').text(''); 
});

_submit('#kermaForm', function(data, msg){
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
                    reload_ajax('kerma')
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
    $('#kermaForm').trigger('submit');
});
function hapus(id){
    _hapus(id, 'kerja_sama/hapusAjax',function(){
        setTimeout(function() {
            swal.close()
            swal.fire({
                "title": "Sukses",
                "text": "Data Berhasil dihapus",
                "icon": "success"
            }).then((data) => {
                if (data.value) {
                    reload_ajax('kerma');
                }
            });
        }, 800);
    })
}
function edit(id){
    _edit(id, 'kerja_sama/editAjax', function(data){
        console.log(data);
        kerma = data.data;
        $('#kermaForm [name="mode"]').val('edit');
        $('#kermaForm [name="id"]').val(kerma.id)
        $('#kermaForm [name="referensi"]').val(kerma.referensi)
        $('#kermaForm [name="judul"]').val(kerma.judul)
        $('#kermaForm [name="mitra"]').val(kerma.mitra)
        $('#kermaForm [name="unit_id"]').val(kerma.unit_id).trigger('change');
        $('#kermaForm [name="jenis_id"]').val(kerma.jenis_id).trigger('change');
        $('#kermaForm [name="tipe_id"]').val(kerma.tipe_id).trigger('change');
        $('#kermaForm [name="kategori_id"]').val(kerma.kategori_id).trigger('change');
        $('#kermaForm [name="bentuk_id"]').val(kerma.bentuk_id).trigger('change');
        $('#kermaForm [name="tanggal_mulai"]').val(kerma.tanggal_mulai)
        $('#kermaForm [name="tanggal_selesai"]').val(kerma.tanggal_selesai)
        
        link = kerma.dokumen != '' ? base + 'uploads/kerma/' +kerma.dokumen : '';
        init_dropify('#dokumen', link);

        setTimeout(function() { 
            swal.close()
            myModal.show();
        }, 800);
    })
}

$('#export').on('click', function(){
    console.log('export');
    
    // Ambil nilai filter dari elemen input / select
    var jenis  = $('#filter_jenis').val();
    var unit   = $('#filter_unit').val();
    var tipe   = $('#filter_tipe').val();
    var status = $('#filter_status_kerma').val(); // contoh tambahan
    var aktif  = $('#filter_status').val();        // contoh tambahan untuk status aktif/selesai
    var kategori = $('#filter_kategori').val();
    
    // Susun query string (hanya kirim jika ada nilainya)
    var query = [];
    if (jenis) query.push('jenis=' + jenis);
    if (unit) query.push('unit=' + unit);
    if (tipe) query.push('tipe=' + tipe);
    if (status) query.push('status_kerma=' + status);
    if (aktif) query.push('filter_status=' + aktif);
    if (kategori) query.push('kategori=' + kategori);
    
    var queryString = query.length ? '?' + query.join('&') : '';

    ajaxcsrf(); // tetap pertahankan gaya kamu

    // Gunakan fileDownload dengan method GET dan query string
    $.fileDownload(site + 'kerja_sama/export' + queryString, {
        httpMethod: 'GET',
        successCallback: function (url) {
            console.log('berhasil');
            Swal.fire({
                title: 'Success',
                text: 'Export Berhasil',
                icon: 'success'
            });
        },
        failCallback: function (responseHtml, url) {
            Swal.fire({
                title: 'Failed',
                text: 'Export Gagal',
                icon: 'error'
            });
            console.log('gagal');
        }
    });
});
