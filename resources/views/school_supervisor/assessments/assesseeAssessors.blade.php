@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('content')
    <div class="row">
        @if (!empty($assessmentUsers))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">{{ trans('assessment.assessor_name') }}</th>
                                        <th class="text-center">{{ trans('assessment.branch') }}</th>
                                        <th class="text-center">{{ trans('assessment.average_score') }}</th>
                                        <th></th>
                                    </tr>
                                </thead-dark>
                                <tbody>
                                    @php
                                        $pageNumber = request()->query('page') ? request()->query('page') - 1 : 0;
                                        $serial = $pageNumber * env('PAGE_LIMIT', 15) + 1;
                                    @endphp
                                    @foreach ($assessmentUsers as $assessmentUser)
                                        @php
                                            if ($assessmentUser->assessor->type == \App\OurEdu\Users\UserEnums::SCHOOL_ACCOUNT_MANAGER) {
                                              $branches = $assessmentUser->assessment->schoolAccount->branches()->pluck("name")->toArray();
                                              $branch = implode(', ', $branches);
                                          } else if($assessmentUser->assessor->type ==  \App\OurEdu\Users\UserEnums::EDUCATIONAL_SUPERVISOR && $assessmentUser->assessor->branches()->count()>0){
                                             $branch= implode(', ' ,$assessmentUser->assessor->branches->pluck('name')->toArray());
                                          }else{
                                            $branch = $assessmentUser->assessor->schoolAccountBranchType->schoolAccount->name.': '.$assessmentUser->assessor->schoolAccountBranchType->name;
                                          }

                                        @endphp
                                        <tr class="text-center">
                                            <td>{{ $serial}}</td>
                                            <td>{{ $assessmentUser->assessor->name ?? '' }}</td>
                                            <td>{{$branch }}</td>
                                            <td>{{ number_format(($assessmentUser->average_score/$assessmentUser->ave_total_mark)*100, 2) }}%</td>
                                            <td>
                                               <a class="btn btn-primary btn-xs" href="{{ route('school-branch-supervisor.assessee.answers.list',[
                                        "assessment"=>$assessmentUser->assessment ,"assessee"=>$assessmentUser->assessee, "assessor"=>$assessmentUser->assessor ]) }}">
                                                        {{trans('assessment.View answers')}}
                                                    </a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                {{ $assessmentUsers->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
