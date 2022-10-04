@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">

        <div class="col-md-2 col-sm-2 col-xs-2" data-step="2" data-intro="The All Tasks Here!"  data-position='right' >
            <a href="{{ route('admin.subjects.get.export.student-grades').'?'.request()->getQueryString() }}" class="btn btn-primary">{{ trans('subjects.Export') }}</a>
        </div>
    </div>
@endpush
@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('exams.Subject') }}</th>
                    <th class="text-center">{{ trans('exams.Number of exams') }}</th>
                    <th class="text-center">{{ trans('exams.Result') }}</th>
                    <th class="text-center">{{ trans('exams.Educational system') }}</th>
                    <th class="text-center">{{ trans('exams.Country') }}</th>
                    <th class="text-center">{{ trans('exams.Grade class') }}</th>
                    <th class="text-center">{{ trans('subjects.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->name }}</td>
                        <td>{{ @$row->exams_count ?? '' }}</td>
                        <td>{{ $row->exams_count > 0 ? number_format($row->exams->sum('result') / $row->exams_count,2)  : number_format($row->exams->average('result'),2) }}</td>
                        <td>{{ @$row->educationalSystem->name ?? '' }}</td>
                        <td>{{ @$row->country->name  ?? ''}}</td>
                        <td>{{ @$row->gradeClass->title ?? '' }}</td>
                        <td>
                            <div class="form-group">
                                <a class="btn btn-xs btn-primary" href="{{  route('admin.exams.get.getStudentGrades',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('subject.view') }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pull-right">
                {{ $rows->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection


@push('js')

    <script>
        $(function () {
                // DOM Ready
                @if(request('country_id'))
                    $('#country_id').trigger('change');
                @endif
            });

        $('#country_id').change(function () {
                $("#educational_system_id").empty();
                $.get('{{ route('admin.exams.countrySystems') }}',
                    {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        country_id: $(this).val()
                    })
                    .then(function (response) {
                        $.each(response.systems, function (i, item) {
                            $('#educational_system_id').append(`<option value="${i}">${item}</option>`);
                        });
                    });

                setTimeout(function(){
                        $('#educational_system_id').trigger('change');
                    }, 700);

            });

        $('#educational_system_id').change(function () {
                $("#subject_id").empty();

                if($(this).val() == null){
                    return;
                }

                $.get('{{ route('admin.exams.systemSubjects') }}',
                    {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        educational_system_id: $(this).val()
                    })
                    .then(function (response) {
                        $.each(response.subjects, function (i, item) {
                            $('#subject_id').append(`<option value="${i}">${item}</option>`);
                        });
                    });
            });
    </script>
@endpush
