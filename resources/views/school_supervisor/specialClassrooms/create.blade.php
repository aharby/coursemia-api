
@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['method' => 'post','route'=>['school-branch-supervisor.specialClassrooms.post.create'],'class'=>'form-vertical form-label-left' ,  "enctype"=>"multipart/form-data"]) }}
                    @include('school_supervisor.classrooms.form',['special' => true])
                    <div class="form-group">
                        <div class="form-layout-footer">
                            <button type="submit" class="btn btn-success"> {{ trans('app.Save') }}</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div></div></div>
    </div>
@endsection
@push('scripts')
    <script>

        $('#grade_class_id').on('change',function (e){
            var grade = $(this).val();

            if (grade == ""){
                $('#student').attr('disabled',true);
                return ;
            }

            $('#student').attr('disabled',false);

            var grade_class_id = $('#grade_class_id').val();
            var educational_system_id = $('#educational_system_id').val();
            var academic_year_id = $('#academic_year_id').val();
            var educational_term_id = $('#educational_term_id').val();

            $.get('{{ route('school-branch-supervisor.classrooms.classroomClasses.getClassroomStudents') }}',
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'grade_class_id': grade_class_id,
                    'educational_system_id': educational_system_id,
                    'academic_year_id': academic_year_id,
                    'educational_term_id': educational_term_id,
                })
                .done(function (response) {
                    $('#student').empty();
                    $.each(response, function (i, item) {
                        $('#student').append('<option value="'+item.id+'">'+item.user.name+'</option>');
                    });
                });

        });
    </script>
@endpush

