$(document).ready(function(){
    // $('.select2').each(function(index, element) {
    //     var initValue = element.dataset.value != undefined ? element.dataset.value : false;
    //     var item = $(element);
    //     if (item.data('url')) {
    //         CustomInitSelect2(item, {
    //             url: item.data('url'),
    //             initialValue: initValue,
    //         }, myModalEl);
    //     } else {
    //         item.select2();
    //     }
    // });
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
    ];
    default_table_init('penerapan', 'penerapan/data_penerapan', kolom, kolom_def);
});