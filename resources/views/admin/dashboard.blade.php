@extends('layouts.master')
@push('css')
    <link rel="stylesheet" href="{{ asset('dist') }}/assets/extensions/simple-datatables/style.css">
    <link rel="stylesheet" href="{{ asset('dist') }}/assets/compiled/css/table-datatable.css">
@endpush
@section('header')
    <h3>Dashboard</h3>
@endsection
@section('content')
    <section class="row">
        <div class="col-12 col-lg-12">
            <div class="row">
                <div class="col-12">
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body px-4 py-4">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <h6 class="text-primary">Ringkasan Aduan</h6>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="stats-icon red mb-2">
                                                <i class="bi bi-file-text-fill"></i>
                                            </div>
                                            <h6 class="text-muted">Total Aduan: {{ $aduanTotal }}</h6>
                                            <div class="mt-2">
                                                <small class="text-danger d-block">Aduan Aset: {{ $aduanAsetTotal }}</small>
                                                <small class="text-primary d-block">Aduan GA: {{ $aduanGATotal }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body px-4 py-4">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <h6 class="text-primary">Ringkasan Ajuan</h6>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="stats-icon green mb-2">
                                                <i class="bi bi-cart-fill"></i>
                                            </div>
                                            <h6 class="text-muted">Total Ajuan: {{ $ajuanTotal }}</h6>
                                            <div class="mt-2">
                                                <small class="text-success d-block">Ajuan RTK: {{ $ajuanRTKTotal }}</small>
                                                <small class="text-warning d-block">Ajuan ATK: {{ $ajuanATKTotal }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Grafik Aduan</h4>
                        </div>
                        <div class="card-body">
                            <div id="aduan-chart" style="width:100%; height:400px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Grafik Ajuan</h4>
                        </div>
                        <div class="card-body">
                            <div id="ajuan-chart" style="width:100%; height:400px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('js')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chart = Highcharts.chart('aduan-chart', {
                chart: {
                    type: 'line'
                },
                title: {
                    text: 'Grafik Aduan Bulanan'
                },
                xAxis: {
                    categories: @json($months),
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Jumlah Aduan'
                    }
                },
                series: [
                    {
                        name: 'Aset',
                        data: @json($chartDataAset),
                        color: 'red'
                    },
                    {
                        name: 'GA',
                        data: @json($chartDataGA),
                        color: 'blue'
                    }
                ]
            });

            const ajuanChart = Highcharts.chart('ajuan-chart', {
                chart: {
                    type: 'line'
                },
                title: {
                    text: 'Grafik Ajuan Bulanan'
                },
                xAxis: {
                    categories: @json($months),
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Jumlah Ajuan'
                    }
                },
                series: [
                    {
                        name: 'RTK',
                        data: @json($chartDataRTK),
                        color: 'green'
                    },
                    {
                        name: 'ATK',
                        data: @json($chartDataATK),
                        color: 'orange'
                    }
                ]
            });

            function updateChart() {
                $.ajax({
                    url: '{{ route("admin.dashboard.data") }}',
                    method: 'GET',
                    success: function (response) {
                        chart.series[0].setData(response.chartDataAset);
                        chart.series[1].setData(response.chartDataGA);
                        ajuanChart.series[0].setData(response.chartDataRTK);
                        ajuanChart.series[1].setData(response.chartDataATK);
                    }
                });
            }

            // Update chart setiap 5 detik (Dinonaktifkan sementara untuk performa)
            // setInterval(updateChart, 5000);
        });
    </script>
@endpush
