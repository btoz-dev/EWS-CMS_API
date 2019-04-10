@extends('layouts.cmsApp')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
                <!-- <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
                <button type="button" class="btn btn-sm btn-outline-secondary">Export</button> -->
            </div>
            <!-- <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                <span data-feather="calendar"></span>
                This week
            </button> -->
        </div>
    </div>
    <div class="row text-center">
      <div class="col-lg-2">
        <i class="fas fa-users fa-5x"></i>
        <h6>User Management</h6>
        <p>Pengelolaan akun</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/usermgmt') }}">View details »</a></p>
      </div>
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>RKM Reports</h6>
        <p>Jadwal RKM mandor keseluruhan</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/rkmReport') }}">View details »</a></p>
      </div>
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>Mandor Trans Reports</h6>
        <p>Hasil aktifitas mandor</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/mandorTransReport') }}">View details »</a></p>
      </div>
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>Kawil Trans Reports</h6>
        <p>Hasil aktifitas kawil</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/kawilTransReport') }}">View details »</a></p>
      </div>
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>Custom Reports</h6>
        <p>Tabel pencarian khusus</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/customReport') }}">View details »</a></p>
      </div>
      <div class="col-lg-2">
        <i class="fab fa-android fa-5x"></i>
        <h6>Download APK APP</h6>
        <p>Download apk android terbaru</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ asset('storage/ews-app-release_22-03-2019.apk') }}">View details »</a></p>
      </div>
    </div>
    <!-- <canvas id="myChart2"></canvas> -->
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.9.0/feather.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script> -->
    <!-- <script src="{{ asset('js/dashboard.js') }}"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var ctx = document.getElementById('myChart2').getContext('2d');
        var chart = new Chart(ctx, {
            // The type of chart we want to create
            type: 'pie',

            // The data for our dataset
            data : {
                datasets: [{
                    data: [],
                    backgroundColor: [
                        'rgb(54, 162, 235)',
                        'rgb(255, 99, 132)',
                        // window.chartColors.blue,
                        // window.chartColors.red,
                    ]
                }],

                // These labels appear in the legend and in the tooltips when hovering different arcs
                labels: [
                    'Sudah di realisasi',
                    'Belum di realisasi',
                ]
            },

            // Configuration options go here
            options: {}
        });

        // $.post('{{route('dashboard.getDataChart')}}', {
        //         type: 'blok', 
        //         id: $('#select-aktifitas').val()
        //     }, function (e) {
        //     // body...
        //     $('#select-codeBlok').html(e);
        //     $('#select-codeBlok').selectpicker('refresh');
        // })

        $.ajax({
            url: '{{route('dashboard.getDataChart')}}',
            method: 'post',
            data: {
                rkhCode : 'RKH/KL01/0319/0292',
                codeAlojob : '5210400100',
                codeBlok : '2031-R0',
            },
            success: function(data) {
                console.log(data);
              // process your data to pull out what you plan to use to update the chart
              // e.g. new label and a new data point
              
              // add new label and data point to chart's underlying data structures
              // myChart.data.labels.push("Post " + postId++);
              chart.data.datasets[0].data.push(data.pokokDone);
              chart.data.datasets[0].data.push(data.totalPokok - data.pokokDone);
              
              // // re-render the chart
              chart.update();
            }
        });

        // logic to get new data
        var getData = function() {
          // $.ajax({
          //   url: '{{route('dashboard.getDataChart')}}',
          //   method: 'post',
          //   success: function(data) {
          //       console.log(data);
          //     // process your data to pull out what you plan to use to update the chart
          //     // e.g. new label and a new data point
              
          //     // add new label and data point to chart's underlying data structures
          //     // myChart.data.labels.push("Post " + postId++);
          //     // myChart.data.datasets[0].data.push(getRandomIntInclusive(1, 25));
              
          //     // // re-render the chart
          //     // myChart.update();
          //   }
          // });
        };
    </script>
@endsection
