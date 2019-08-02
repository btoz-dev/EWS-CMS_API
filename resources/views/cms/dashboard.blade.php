@extends('layouts.cmsApp')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
    </div>
    <div class="row text-center">
      @can('view_usermgmt')
      <div class="col-lg-2">
        <i class="fas fa-users fa-5x"></i>
        <h6>User Management</h6>
        <p>Pengelolaan akun</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/usermgmt') }}">View details »</a></p>
      </div>
      @endcan
      @can('view_roles')
      <div class="col-lg-2">
        <i class="fas fa-users fa-5x"></i>
        <h6>Role Management</h6>
        <p>Pengelolaan role</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/roles') }}">View details »</a></p>
      </div>
      @endcan
      @can('view_clt')
      <div class="col-lg-2">
        <i class="fas fa-users fa-5x"></i>
        <h6>Produk CLT Management</h6>
        <p>Produk Cek List Timbang</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/clt') }}">View details »</a></p>
      </div>
      @endcan
      @can('view_rkmReport')
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>RKM Reports</h6>
        <p>Jadwal RKM mandor keseluruhan</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/rkmReport') }}">View details »</a></p>
      </div>
      @endcan
      @can('view_customReport')
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>Custom Reports</h6>
        <p>Tabel pencarian khusus</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/customReport') }}">View details »</a></p>
      </div>
      @endcan
      @can('view_apk')
      <div class="col-lg-2">
        <i class="fab fa-android fa-5x"></i>
        <h6>Download APK APP</h6>
        <p>Download apk android terbaru</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ asset('storage/ews-app-release_22-03-2019.apk') }}">View details »</a></p>
      </div>
      @endcan
    </div>
    <div class="row text-center">
      @can('view_mandorPlantReport')
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>Mandor Plantcare Reports</h6>
        <p>Laporan mandor pada aktifitas plantcare</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/mandorPlantcareReport') }}">View details »</a></p>
      </div>
      @endcan
      @can('view_mandorFruitReport')
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>Mandor Fruitcare Reports</h6>
        <p>Laporan mandor pada aktifitas fruitcare</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/mandorFruitcareReport') }}">View details »</a></p>
      </div>
      @endcan
      @can('view_mandorPanenReport')
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>Mandor Fruitcare Reports</h6>
        <p>Laporan mandor pada aktifitas panen</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/mandorPanenReport') }}">View details »</a></p>
      </div>
      @endcan
      @can('view_kawilPlantReport')
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>Kawil Plantcare Reports</h6>
        <p>Laporan kawil pada aktifitas plantcare</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/kawilPlantcareReport') }}">View details »</a></p>
      </div>
      @endcan
      @can('view_kawilFruitReport')
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>Kawil Fruitcare Reports</h6>
        <p>Laporan kawil pada aktifitas fruitcare</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/kawilFruitcareReport') }}">View details »</a></p>
      </div>
      @endcan
      @can('view_kawilPanenReport')
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>Kawil Fruitcare Reports</h6>
        <p>Laporan kawil pada aktifitas panen</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/kawilPanenReport') }}">View details »</a></p>
      </div>
      @endcan
    </div>
    <div class="row text-center">
      @can('view_phTBReport')
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>Packing House Report</h6>
        <p>Laporan PH penghitungan Tandan</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/phtbReport') }}">View details »</a></p>
      </div>
      @endcan
      @can('view_phBTReport')
      <!-- <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>Packing House Report</h6>
        <p>Laporan PH penghitungan Berat Tandan</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/phbtReport') }}">View details »</a></p>
      </div> -->
      @endcan
      @can('view_phBBReport')
      <!-- <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>Packing House Report</h6>
        <p>Laporan PH penghitungan Berat Bonggol</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/phbbReport') }}">View details »</a></p>
      </div> -->
      @endcan
      @can('view_phHTReport')
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>Packing House Report</h6>
        <p>Laporan quality control</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/phhtReport') }}">View details »</a></p>
      </div>
      @endcan
      @can('view_phCLTReport')
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>Packing House Report</h6>
        <p>Laporan cek list timbang</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/phcltReport') }}">View details »</a></p>
      </div>
      @endcan
      @can('view_spiMandorReport')
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>SPI</h6>
        <p>Laporan Pemeriksaan RKH</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/spiMandorReport') }}">View details »</a></p>
      </div>
      @endcan
      @can('view_spiSensusReport')
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>SPI</h6>
        <p>Laporan Sensus Pokok</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/spiSensusReport') }}">View details »</a></p>
      </div>
      @endcan
    </div>
    <!-- <canvas id="myChart2"></canvas> -->
@endsection

@section('script')
  <script src="{{ asset('js/feather.min.js') }}"></script>
  <script src="{{ asset('js/chart.js') }}"></script>
  <!-- <script src="{{ asset('js/dashboard.js') }}"></script> -->
@endsection
