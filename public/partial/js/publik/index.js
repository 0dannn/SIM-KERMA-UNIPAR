var donutChartCanvas = $('#donutChart').get(0).getContext('2d');
var myChart = new Chart(donutChartCanvas, {
    type: 'doughnut',
    data: {
        labels: [],
        datasets: []
    },
    options: {
        maintainAspectRatio: false,
        responsive: true,
        cutoutPercentage: 65,
        legend: {
            position: 'bottom',
            labels: {
                boxWidth: 12,
                padding: 12,
                fontSize: 11
            }
        },
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    var dataset = data.datasets[tooltipItem.datasetIndex];
                    var currentValue = dataset.data[tooltipItem.index];
                    return ' ' + data.labels[tooltipItem.index] + ': ' + currentValue + ' Kerjasama';
                }
            }
        }
    }
});

function get_data() {
    const newDataset = {
        label: 'Unit Kerja',
        backgroundColor: [],
        data: [],
        borderWidth: 2,
        borderColor: '#ffffff'
    };

    ajaxcsrf();
    $.ajax({
      url: site + 'publik/get_chart',
      method: 'POST',
      dataType: 'JSON',
      success: function(data) {
        data.forEach(function(item){
            newDataset.data.push(item.value);
            newDataset.backgroundColor.push(item.color);
            myChart.data.labels.push(item.prodi);
        });
        myChart.data.datasets.push(newDataset);
        myChart.update();
      }
    });
}

kolom = [
    {
        data: "id",
        orderable: false,
        searchable: false,
        className: "text-center fw-bold",
        render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
        }
    },
    { 
        data: "nama",
        render: function(data) {
            return '<span class="fw-semibold text-dark"><i class="fa fa-folder me-2 text-primary"></i>' + data + '</span>';
        }
    },
    { 
        data: "jumlah",
        className: "text-center",
        render: function(data) {
            return '<span class="badge bg-soft-info text-info font-size-12 px-3 py-1 rounded-pill">' + data + ' Dokumen</span>';
        }
    },
];

$(document).ready(function(){
    get_data();
    simple_datatables('jumlah_kerma', 'publik/jenis_data', kolom);
});