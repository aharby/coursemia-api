<table class="table table-striped">
    <tbody>
        <tr>
            <th width="25%" class="text-center">{{ trans('tasks.Title') }}</th>
            <td width
            ="75%" class="text-center">{{ $task->title }}</td>
        </tr>


                    <tr>
                        <th width="25%" class="text-center">{{ trans('tasks.Content Author') }}</th>
                        <td width="75%" class="text-center">
                            {{$contentAuthor->first_name}}
                            {{$contentAuthor->last_name}}
                        </td>
                    </tr>

                        @if($criteria = json_decode($resource->accept_criteria))
                            <tr>
                                <th width="25%" class="text-center">{{ trans('tasks.Description') }}</th>
                                <td width="75%" class="text-center">{{ $criteria->description ?? '' }}</td>
                            </tr>

                            <tr>
                                <th width="25%" class="text-center">{{ trans('tasks.Due date') }}</th>
                                <td width="75%" class="text-center">{{ $criteria->due_date ?? '' }}</td>
                            </tr>


                            @if(in_array($resource->resource_slug, \App\OurEdu\LearningResources\Enums\LearningResourcesEnums::getQuestionLearningResources()))
                                <tr>
                                    <th width="25%" class="text-center">{{ trans('tasks.Difficulty Level') }}</th>
                                    <td width="75%" class="text-center">{{ (isset($criteria->difficulty_level) && $criteria->difficulty_level) ? ($options->where('id', $criteria->difficulty_level)->first()->title ?? '') : '' }}</td>
                                </tr>
                            @endif

                            @if($resource->resource_slug == \App\OurEdu\LearningResources\Enums\LearningResourcesEnums::DRAG_DROP)
                                <tr>
                                    <th width="25%" class="text-center">{{ trans('tasks.Drag and Drop type') }}</th>
                                    <td width="75%" class="text-center">{{ @$criteria->drag_drop_type ? ($options->where('id', $criteria->drag_drop_type)->first()->title ?? '') : '' }}</td>
                                </tr>
                            @endif

                            @if($resource->resource_slug == \App\OurEdu\LearningResources\Enums\LearningResourcesEnums::MULTI_CHOICE)

                                <tr>
                                    <th width="25%" class="text-center">{{ trans('tasks.Multi choice type') }}</th>
                                    <td width="75%" class="text-center">{{ @$criteria->multiple_choice_type ? ($options->where('id', $criteria->multiple_choice_type)->first()->title ?? '') : '' }}</td>
                                </tr>
                            @endif

                            @if($resource->resource_slug == \App\OurEdu\LearningResources\Enums\LearningResourcesEnums::TRUE_FALSE)

                                <tr>
                                    <th width="25%" class="text-center">{{ trans('tasks.True false type') }}</th>
                                    <td width="75%" class="text-center">{{ @$criteria->true_false_type ? ($options->where('id', $criteria->true_false_type)->first()->title ?? '') : '' }}
                                    </td>
                                </tr>
                            @endif
                    @endif

                    <tr>
                        <th width="25%" class="text-center">{{ trans('tasks.Resource slug') }}</th>
                        <td width
                        ="75%" class="text-center">{{ $resource->resource_slug }}</td>
                    </tr>

                    <tr>
                        <th width="25%" class="text-center">{{ trans('tasks.Pull time') }}</th>
                        <td width
                        ="75%" class="text-center">{{ $task->pulled_at ?? trans('tasks.not pulled yet')}}</td>
                    </tr>

                    @switch($resource->resource_slug)
                        @case(\App\OurEdu\LearningResources\Enums\LearningResourcesEnums::TRUE_FALSE)
                            @include('admin.tasks.resources.questions.truefalse' , ['resource' => $resource])
                        @break
                        @case(\App\OurEdu\LearningResources\Enums\LearningResourcesEnums::MULTIPLE_MATCHING)
                            @include('admin.tasks.resources.questions.multiMatching' , ['resource' => $resource])
                        @break
                        @case(\App\OurEdu\LearningResources\Enums\LearningResourcesEnums::MATCHING)
                            @include('admin.tasks.resources.questions.matching' , ['resource' => $resource])
                        @break
                        @case(\App\OurEdu\LearningResources\Enums\LearningResourcesEnums::MULTI_CHOICE)
                            @include('admin.tasks.resources.questions.multipleChoice' , ['resource' => $resource])
                        @break
                        @case(\App\OurEdu\LearningResources\Enums\LearningResourcesEnums::COMPLETE)
                            @include('admin.tasks.resources.questions.complete' , ['resource' => $resource])
                        @break
                        @case(\App\OurEdu\LearningResources\Enums\LearningResourcesEnums::DRAG_DROP)
                            @include('admin.tasks.resources.questions.dragdrop' , ['resource' => $resource])
                        @break
                        @case(\App\OurEdu\LearningResources\Enums\LearningResourcesEnums::Audio)
                            @include('admin.tasks.resources.media.audio' , ['resource' => $resource])
                        @break
                        @case(\App\OurEdu\LearningResources\Enums\LearningResourcesEnums::FLASH)
                            @include('admin.tasks.resources.media.flash' , ['resource' => $resource])
                        @break
                        @case(\App\OurEdu\LearningResources\Enums\LearningResourcesEnums::PAGE)
                            @include('admin.tasks.resources.media.page' , ['resource' => $resource])
                        @break
                        @case(\App\OurEdu\LearningResources\Enums\LearningResourcesEnums::PDF)
                            @include('admin.tasks.resources.media.pdf' , ['resource' => $resource])
                        @break
                        @case(\App\OurEdu\LearningResources\Enums\LearningResourcesEnums::PICTURE)
                            @include('admin.tasks.resources.media.picture' , ['resource' => $resource])
                        @break
                        @case(\App\OurEdu\LearningResources\Enums\LearningResourcesEnums::Video)
                            @include('admin.tasks.resources.media.video' , ['resource' => $resource])
                        @break
                        @default
                            <h4>{{trans('tasks.No Available Details')}}</h4>
                        @break
                    @endswitch
                </tbody>
            </table>
