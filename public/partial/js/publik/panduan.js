$(document).ready(function(){
    if (document.getElementById('panduan')) {
        columns = [
            {
                data: "id",
                orderable: false,
                searchable: false
            },
            { data: "judul" },
            { data: "deskripsi" },
            { data: "tanggal" },
            {
                data: "dokumen",
                orderable: false,
                searchable: false
            },
        ];
        columnDefs = [
            {
                targets: 4,
                data: "dokumen",
                render: function(data){
                    // return `<button type="button" class="btn btn-sm btn-success downBtn" onclick="down_file('${data}')"><i class="fa fa-download"></i> 
                    //     Download</button>`;
                    return `<a class="btn btn-sm btn-success download-dokumen" href="${site}/uploads/panduan/${data}">
                    <i class="fa fa-file-download"></i> Download</a>`;
                }
            },
        ];
        default_table_init('panduan', 'panduan/data', columns, columnDefs);
    }
});