<!-- resources/views/dashboard.blade.php -->
@extends('layouts.master')
@push('css')
    <link rel="stylesheet" href="{{ asset('dist') }}/assets/extensions/simple-datatables/style.css">
    <link rel="stylesheet" href="{{ asset('dist') }}/assets/compiled/css/table-datatable.css">
@endpush
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger mt-3">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
    <script src="{{ asset('dist') }}/assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
    <script src="{{ asset('dist') }}/assets/static/js/pages/simple-datatables.js"></script>
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
                series: [{
                    name: 'Aduan',
                    data: @json($chartData)
                }]
            });

            function updateChart() {
                $.ajax({
                    url: '{{ route("admin.dashboard.data") }}',
                    method: 'GET',
                    success: function (response) {
                        chart.series[0].setData(response.chartData);
                    }
                });
            }

            // Update chart every 5 seconds
            setInterval(updateChart, 5000);
        });
    </script>
@endpush