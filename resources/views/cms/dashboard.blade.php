@extends('layouts.cmsApp')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
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
        <h6>Custom Reports</h6>
        <p>Tabel pencarian khusus</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/customReport') }}">View details »</a></p>
      </div>
    </div>
    <div class="row text-center">
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>SPI Plantcare Reports</h6>
        <p>Laporan SPI pada aktifitas plantcare</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/mandorPlantcareReport') }}">View details »</a></p>
      </div>
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>Mandor Fruitcare Reports</h6>
        <p>Laporan mandor pada aktifitas fruitcare</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/mandorFruitcareReport') }}">View details »</a></p>
      </div>
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>Kawil Plantcare Reports</h6>
        <p>Laporan kawil pada aktifitas plantcare</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/kawilPlantcareReport') }}">View details »</a></p>
      </div>
      <div class="col-lg-2">
        <i class="far fa-chart-bar fa-5x"></i>
        <h6>Kawil Fruitcare Reports</h6>
        <p>Laporan kawil pada aktifitas fruitcare</p>
        <p><a role="button" class="btn btn-secondary btn-sm" href="{{ url('/kawilFruitcareReport') }}">View details »</a></p>
      </div>
    </div>
    <div class="row text-center">
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
  <script src="{{ asset('js/feather.min.js') }}"></script>
  <script src="{{ asset('js/chart.js') }}"></script>
  <!-- <script src="{{ asset('js/dashboard.js') }}"></script> -->
@endsection
