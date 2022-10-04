{{--<a href="{{url('students-sample.xls')}}" ><h4>{{ trans('app.Excel Sample file') }}</h4></a>--}}

@include('form.select',['name'=>'grade_class_id','options'=> $gradeClasses ,'value' => $row->gradeClass ?? null ,
'attributes'=>['id'=>'grade_class_id','class'=>'form-control', isset($row) ? 'disabled':'' , 'required'=>'required','label'=>trans('classrooms.grade classes'),'placeholder'=>trans('classrooms.grade classes')]])


@include('form.select',['name'=>'educational_system_id','options'=> $educationalSystems , 'value' => $row->educationalSystem ?? null ,
'attributes'=>['id'=>'educational_system_id','class'=>'form-control', isset($row) ? 'disabled':'' ,'required'=>'required','label'=>trans('classrooms.educational system'),'placeholder'=>trans('classrooms.educational system')]])


@include('form.input',['type'=>'text','name'=>'name','value'=> $row->name ?? null,
'attributes'=>['class'=>'form-control', 'label'=>trans('classrooms.name'),'placeholder'=>trans('classrooms.name'),'required'=>'required']])


@include('form.select',['name'=>'academic_year_id','options'=> $academicYears , 'value'=> $row->academicYear ?? null ,
'attributes'=>['id'=>'academic_year_id','class'=>'form-control', isset($row) ? 'disabled':'' ,'label'=>trans('classrooms.Academic Year'),'placeholder'=>trans('classrooms.Academic Year')]])

@include('form.select',['name'=>'educational_term_id','options'=> $educationalTerms , 'value'=> $row->educationalTerm ?? null ,
'attributes'=>['id'=>'educational_term_id','class'=>'form-control', isset($row) ? 'disabled':'' ,'label'=>trans('classrooms.Educational Term'),'placeholder'=>trans('classrooms.Educational Term')]])

@if(!$special)
<div class="row mg-t-20 form-group">
    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">
        {{ trans('app.import students') }}
    </label>
    <div class="col-md-6 col-sm-6 col-xs-12">

        <input type="file" class="form-control" name="file" id="file">
        <p class="alert-info">{{ trans('app.Please use the attached file') }}</p>
    </div>
</div>
@else

    <div class="row mg-t-20 form-group">
        <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">
            {{trans('classrooms.students')}}
        </label>
        <div class="col-md-6 col-sm-6 col-xs-12">

            <select name="students[]" class="form-control select2"  multiple id="student" {{isset($row) ? '' : 'disabled'}}>
                @if(isset($row))
                    @foreach($students as $student)
                        <option value="{{$student->id}}" {{$row->specialStudents->contains($student->id) ? 'selected' : ''}}>{{$student->user->name}}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
@endif
