@extends('layouts.school_admin_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('content')
    <div class="row">
        @if(!empty($sections))
            @php $counter = 0; @endphp
            @foreach($sections as  $key => $section)

                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-body">
                                <canvas id="bar-chart_{{++$counter}}" width="800" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            @endforeach
            <div class="pull-right">
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection

@push("scripts")
<script>
    @php $counter = 0; @endphp

    @foreach($sections as  $key => $section)
    // Bar chart
    new Chart(document.getElementById("bar-chart_{{++$counter}}"), {
        type: 'bar',
        data: {
            labels: ["{{trans('quiz.students count')}}", "{{trans('general_quizzes.Average Score')}}", "{{trans('quiz.min score')}}", "{{trans('quiz.max score')}}"],
            datasets: [
                {
                    // label: "Population (millions)",
                    backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f", "#3c5a9f"],
                    data: [
                        "{{ $generalQuiz->studentsAnswered->count() ?? 0 }}",
                        "@if(isset($sectionGrades[$key]) && $sectionGrades[$key]->sum('grade') > 0 && $generalQuiz->studentsAnswered->count() >0) {{round((($section->sum('total_score')/$generalQuiz->studentsAnswered->count())/$sectionGrades[$key]->sum('grade')) * 100 ,2)}} @else 0 @endif",
                        "{{ $section->min('total_score') ?? 0}}",
                        "{{ $section->max('total_score') ?? 0}}"
                    ]
                }
            ]
        },
        options: {
            legend: { display: false },
            title: {
                display: true,
                text: "{{trans('quiz.section') }} : {{ $key ??''}}"
            }
        }
    });
    @endforeach
</script>
@endpush
