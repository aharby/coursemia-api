@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <canvas id="pie-chart" width="800" height="450"></canvas>
        </div>
    </div>
@endsection

@push("scripts")
    <script>

        var colors = [];
        while (colors.length < {{ count($labels) }}) {
            do {
                var color = Math.floor((Math.random()*1000000)+1);
            } while (colors.indexOf(color) >= 0);
            colors.push("#" + ("000000" + color.toString(16)).slice(-6));
        }

        new Chart(document.getElementById("pie-chart"), {
            type: 'pie',
            data: {
                labels: [<?php echo '"'.implode('","', $labels).'"' ?>],
                datasets: [{
                    label: "Total Successful Percentage",
                    backgroundColor: colors,
                    data: [<?php echo '"'.implode('","', $percentagesData).'"' ?>]
                }]
            },
            options: {
                title: {
                    display: false,
                    text: 'Predicted world population (millions) in 2050'
                }
            }
        });

    </script>
@endpush

