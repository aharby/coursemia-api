@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.psychological_tests.get.edit',$row->id) }}"
           class="btn btn-success">{{ trans('app.Edit') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>

            <tr>
                <th class="text-center">{{ trans('users.Picture') }}</th>
                <td class="text-center">{!! viewImage($row->picture, 'large') !!}</td>
            </tr>

            @foreach(config("translatable.locales") as $lang)
                <tr>
                    <td width="25%" class="text-center">{{trans('psychological_tests.Name').' '.$lang}}</td>
                    <td width="75%" class="text-center">{{$row->translateOrDefault($lang)->name}}</td>
                </tr>
            @endforeach

            @foreach(config("translatable.locales") as $lang)
                <tr>
                    <td width="25%" class="text-center">{{trans('psychological_tests.Instructions').' '.$lang}}</td>
                    <td width="75%" class="text-center">{!! $row->translateOrDefault($lang)->instructions !!}</td>
                </tr>
            @endforeach

            <tr>
                <th class="text-center">{{ trans('psychological_tests.Is active') }}</th>
                <td class="text-center">{!!  $row->is_active ? '<span class="label label-primary">'.trans('app.active') : '<span class="label label-danger">'.trans('app.not active') !!}</td>
            </tr>

            <tr>
                <th class="text-center">{{ trans('psychological_tests.Total tests') }}</th>
                <td class="text-center">{{ $row->results()->count() }}</td>
            </tr>

            <tr>
                <th class="text-center">{{ trans('psychological_tests.Statistics') }}</th>
                <td class="text-center">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="text-dark">{{ trans('psychological_tests.Results overview') }}</h2>
                        </div>
                        <div class="col-12">
                            <canvas id="bar-chart" width="850" height="450"></canvas>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection


                    



@push('js')
    <script>

            new Chart(document.getElementById("bar-chart"), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        data: {!! json_encode($chartValues) !!},
                        label: "{{ trans('psychological_tests.Results') }}",
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.9)',
                            'rgba(54, 162, 235, 0.9)',
                            'rgba(255, 206, 86, 0.9)',
                            'rgba(75, 192, 192, 0.9)',
                            'rgba(153, 102, 255, 0.9)',
                            'rgba(255, 159, 64, 0.9)',
                            'rgba(255, 99, 132, 0.9)',
                            'rgba(54, 162, 235, 0.9)',
                            'rgba(255, 206, 86, 0.9)',
                            'rgba(75, 192, 192, 0.9)',
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                        ],
                        borderWidth: 1
                    }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    title:{
                        display: true,
                        text: "{{ trans('psychological_tests.Results') }}",
                        fontSize:25
                    },
                    legend:{
                        display: true
                    },
                    scales: {
                        xAxes: [{
                            barThickness : 40
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }],
                    }
                }
            });
            
    </script>

@endpush