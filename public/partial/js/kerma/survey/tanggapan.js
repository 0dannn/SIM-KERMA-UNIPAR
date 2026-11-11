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
    // ambil id surat dari form untuk menampilkan komen berdasarkan suratnya
    token = $('#table_tanggapan').data('token')
    console.log('token adalah = ' + token);
    simple_datatables('table_tanggapan', 'survey/data_tanggapan/' + token, columns, columnDefs);

    $('#printBtn').on('click', function(){
        id = $(this).data('id');
		var nw = window.open(site+"/survey/print_page/"+id,"_blank","width=800,height=600")
			nw.print()
			setTimeout(function(){
				nw.close()
			},2500)
    });
});