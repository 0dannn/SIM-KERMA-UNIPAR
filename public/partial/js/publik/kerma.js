$(document).ready(function(){
    // Inisialisasi filter Select2 untuk Halaman Publik
    CustomInitSelect2($('#filter_jenis'), {url: $('#filter_jenis').data('url'),});
    CustomInitSelect2($('#filter_unit'), {url: $('#filter_unit').data('url'),});
    CustomInitSelect2($('#filter_tipe'), {url: $('#filter_tipe').data('url'),});
    CustomInitSelect2($('#filter_kategori'), {url: $('#filter_kategori').data('url'),});
    $('#filter_status').select2();

    // Pemicu reload data saat filter diubah
    $('.select2-filter').on('change', function() {
        reload_ajax('kerma');
    });

    // Reset Filter
    $('#btn-reset-filter').on('click', function() {
        $('#filter_jenis').val(null);
        $('#filter_unit').val(null);
        $('#filter_tipe').val(null);
        $('#filter_kategori').val(null);
        $('#filter_status').val('');
        $('.select2-filter').trigger('change.select2');
        reload_ajax('kerma');
    });

    if ( ! $.fn.DataTable.isDataTable('#kerma') ) {
        // Setup datatables paging info
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

        $("#kerma").dataTable({
            initComplete: function() {
                var api = this.api();
                $('#mytable_filter input')
                    .off('.DT')
                    .on('input.DT', function() {
                        api.search(this.value).draw();
                });
            },
            oLanguage: {
                sProcessing: "loading..."
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: site + 'publik/data_kerma',
                type: "POST",
                data: function(data){
                    var value = $.cookie('csrf_cookie_name');
                    data[csrfname] = value;
                    $('.select2-filter').each(function(index, element) {
                        var item = $(element);
                        data[item.attr('name')] = item.val();  
                    });
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
                { data: "nm_unit" },
                { data: "mitra" },
                {
                    data: "tanggal_mulai",
                    render: function(data, type, row) {
                        return `${data} s/d ${row.tanggal_selesai}`;
                    }
                },
                { data: "nama_tipe" },
                { 
                    data: "tanggal_selesai",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        var akhir = new Date(data);
                        var today = new Date();
                        if(today <= akhir){
                            return '<span class="badge bg-primary">AKTIF</span>';
                        }else{
                            return '<span class="badge bg-danger">TIDAK AKTIF</span>';
                        }
                    } 
                },
                {
                    data: "dokumen",
                    render: function(data){
                        var button = ' - ';
                        if(data != ''){
                            button = `<a class="btn btn-sm btn-success download-dokumen" href="${site}/uploads/kerma/${data}">
                            <i class="fa fa-file-download"></i> Download</a>`;
                        }
                        return button;
                    }
                }
            ],
            order: [[1, 'asc']],
            rowCallback: function(row, data, iDisplayIndex) {
                var info = this.fnPagingInfo();
                var page = info.iPage;
                var length = info.iLength;
                $('td:eq(0)', row).html();
            }
        });
    } else {
        $('#kerma').DataTable().ajax.reload();
    }
});
