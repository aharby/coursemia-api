@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('exams.Student name') }}</th>
                    <th class="text-center">{{ trans('exams.Result') }}</th>
                    <th class="text-center">{{ trans('exams.Subject') }}</th>
                    <th class="text-center">{{ trans('exams.Educational system') }}</th>
                    <th class="text-center">{{ trans('exams.Country') }}</th>
                    <th class="text-center">{{ trans('exams.Grade class') }}</th>
                    <th class="text-center">{{ trans('exams.School') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ @$row->student->user->name ?? '' }}</td>
                        <td>{{ $row->result ?? 0 }}</td>
                        <td>{{ @$row->subject->name ?? '' }}</td>
                        <td>{{ @$row->subject->educationalSystem->name ?? '' }}</td>
                        <td>{{ @$row->subject->country->name  ?? ''}}</td>
                        <td>{{ @$row->subject->gradeClass->title ?? '' }}</td>
                        <td>{{ @$row->student->school->name ?? '' }}</td>

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
