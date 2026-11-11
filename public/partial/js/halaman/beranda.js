var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
var donutData = {
    labels: [],
    // datasets: [{
    //   data: [1, 1],
    //   backgroundColor: ['#f56954', '#00a65a'],
    // }]
}
var donutOptions = {
    maintainAspectRatio: false,
    responsive: true,
}
//Create pie or douhnut chart
// You can switch between pie and douhnut using the method below.
var myChart = new Chart(donutChartCanvas, {
    type: 'doughnut',
    data: donutData,
    options: donutOptions
})

function get_data() {
    const newDataset = {
        label: 'data set',
        backgroundColor: [],
        data: [],
    };

    ajaxcsrf();
    $.ajax({
      url: site+'beranda/get_chart',
      method: 'POST',
      dataType: 'JSON',
      success: function(data) {
        data.forEach(function(item, index){
            console.log(item);
            newDataset.data.push(item.value);
            newDataset.backgroundColor.push(item.color);
            myChart.data.labels.push(item.prodi)
        })
        myChart.data.datasets.push(newDataset);
        myChart.update();
      }
    })
}
// buat table dibawah sini
kolom = [
    {
        data: "id",
        orderable: false,
        searchable: false,
        render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
        }
    },
    { data: "nama" },
    { data: "jumlah" },
];

$(document).ready(function(){
    get_data();
    simple_datatables('jumlah_kerma', 'beranda/jenis_data', kolom);
})