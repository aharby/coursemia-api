@extends('layouts.school_admin_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('content')
    <div class="row">
        @if(!empty($studentSectionsGrade))

            @php  $key = 1; @endphp
            @foreach($studentSectionsGrade as $section => $studentAnswer)

    
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-header">
                                <table class="table">
                                    <tr>
                                        <th class="text-center">{{ trans('auth.ID') }}</th>
                                        <th class="text-center">{{ trans('quiz.section') }}</th>
                                    </tr>
                                    <tr class="text-center">
                                        <td>{{ $key ??''}}</td>
                                        <td>{{ $section ??''}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="card-body">
                                <canvas id="bar-chart_{{$key}}" width="800" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                @php $key +=1; @endphp
            @endforeach
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection

@push("scripts")
    <script>
        @php  $key = 1; @endphp
        @foreach($studentSectionsGrade as $section => $studentAnswer)
        // Bar chart
        new Chart(document.getElementById("bar-chart_{{$key}}"), {
            type: 'bar',
            data: {
                labels: ["{{ trans('general_quizzes.Student score percentage (per section)') }}", "{{ trans('general_quizzes.General  Average Score percentage (per section)') }}"],
                datasets: [
                    {
                        // label: "Population (millions)",
                        backgroundColor: ["#3e95cd", "#8e5ea2"],
                        data: [
                            @if(isset($sectionGrades[$section]) && $sectionGrades[$section]->sum('grade') > 0 ) {{round(($studentAnswer->sum('total_score')/$sectionGrades[$section]->sum('grade')) * 100 ,2)}} @else 0 @endif,
                            @if(isset($sectionGrades[$section]) && $sectionGrades[$section]->sum('grade') > 0 && $generalQuizStudent->generalQuiz->studentsAnswered->count() >0) {{round((($sectionsStudentsGrade[$section]->sum('total_score')/$generalQuizStudent->generalQuiz->studentsAnswered->count())/$sectionGrades[$section]->sum('grade')) * 100 ,2)}} @else 0 @endif                        
                        ]
                    }
                ]
            },
            options: {
                legend: { display: false },
                title: {
                    display: false,
                    text: 'Predicted world population (millions) in 2050'
                }
            }
        });
        @php $key +=1; @endphp
        @endforeach
    </script>
@endpush
