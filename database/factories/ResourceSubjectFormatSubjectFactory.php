<?php

namespace Database\Factories;

use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\LearningResources\Resource;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;
use App\OurEdu\Options\Option;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResourceSubjectFormatSubjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ResourceSubjectFormatSubject::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $trueFalseType = Option::whereSlug(ResourceOptionsSlugEnum::TRUE_FALSE_WITH_CORRECT)->first()->id;

        $multiChoiceType = Option::whereSlug(ResourceOptionsSlugEnum::MULTIPLE_CHOICE_SLUG_MULTIPLE_CHOICE)->first()->id;

        $dragDropType = Option::whereType('drag_drop_drag_drop_type')->first()->id ?? Option::factory()->create(['slug' => 'image', 'type' => 'drag_drop_drag_drop_type'])->id;

        $difficulty_level = Option::whereType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->inRandomOrder()->first()->id;

        $learning_outcome = Option::whereType(OptionsTypes::RESOURCE_LEARNING_OUTCOME)->inRandomOrder()->first()->id;

        $accept_criteria = [
            'due_date'  =>  4,
            'description' => 'test',
            'difficulty_level'  =>  $difficulty_level,
            'learning_outcome'  =>  $learning_outcome,
            'video_type'    =>  null,
            'true_false_type'   =>  $trueFalseType,
            'drag_drop_type'   =>  $dragDropType,
            'multiple_choice_type'  =>  $multiChoiceType,
        ];

        $resource = Resource::inRandomOrder()->first() ?? Resource::factory()->create();

        return [
            'accept_criteria' => json_encode($accept_criteria),
            'resource_id' => $resource->id,
            'resource_slug' =>  $resource->slug,
            'subject_format_subject_id' => SubjectFormatSubject::factory()->create()->id,
            'is_active' => 1,
            'is_editable' => 1,
            'list_order_key' => 1
        ];
    }

    public function true_false()
    {
        $difficulty_level = Option::whereType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->inRandomOrder()->first()->id;

        $learning_outcome = Option::whereType(OptionsTypes::RESOURCE_LEARNING_OUTCOME)->inRandomOrder()->first()->id;

        $trueFalseType = Option::whereSlug(ResourceOptionsSlugEnum::TRUE_FALSE_WITH_CORRECT)->first()->id;

        $accept_criteria = [
            'due_date'  =>  4,
            'description' => 'true & false description',
            'number_of_question'    =>  10,
            'difficulty_level'  =>  $difficulty_level,
            'learning_outcome'  =>  $learning_outcome,
            'true_false_type'  =>  $trueFalseType,
        ];

        $resource = Resource::whereSlug(LearningResourcesEnums::TRUE_FALSE)->first();

        return $this->state([
            'accept_criteria' => json_encode($accept_criteria),
            'resource_id' => $resource->id,
            'resource_slug' =>  $resource->slug,
            'subject_format_subject_id' => SubjectFormatSubject::factory()->create()->id,
            'is_active' => 1,
            'is_editable' => 1,
            'list_order_key' => 1
        ]);
    }

    public function multiple_choice()
    {
        $difficulty_level = Option::whereType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->inRandomOrder()->first()->id;

        $learning_outcome = Option::whereType(OptionsTypes::RESOURCE_LEARNING_OUTCOME)->inRandomOrder()->first()->id;

        $multiChoiceType = Option::whereSlug(ResourceOptionsSlugEnum::MULTIPLE_CHOICE_SLUG_MULTIPLE_CHOICE)->first()->id;

        $accept_criteria = [
            'due_date'  =>  4,
            'description' => 'multiple choice description',
            'number_of_question'    =>  10,
            'difficulty_level'  =>  $difficulty_level,
            'learning_outcome'  =>  $learning_outcome,
            'multiple_choice_type'  =>  $multiChoiceType,
        ];

        $resource = Resource::whereSlug(LearningResourcesEnums::MULTI_CHOICE)->first();

        return $this->state([
            'accept_criteria' => json_encode($accept_criteria),
            'resource_id' => $resource->id,
            'resource_slug' =>  $resource->slug,
            'subject_format_subject_id' => SubjectFormatSubject::factory()->create()->id,
            'is_active' => 1,
            'is_editable' => 1,
            'list_order_key' => 1
        ]);
    }

    public function video()
    {
        $videoType = Option::whereType(OptionsTypes::VIDEO_VIDEO_TYPE)->first()->id;

        $accept_criteria = [
            'due_date'  =>  4,
            'description' => 'Video description',
            'video_type'  =>  $videoType,
        ];

        $resource = Resource::whereSlug(LearningResourcesEnums::Video)->first();

        return $this->state([
            'accept_criteria' => json_encode($accept_criteria),
            'resource_id' => $resource->id,
            'resource_slug' =>  $resource->slug,
            'subject_format_subject_id' => SubjectFormatSubject::factory()->create()->id,
            'is_active' => 1,
            'is_editable' => 1,
            'list_order_key' => 1
        ]);
    }

    public function audio()
    {
        $audioType = Option::whereType(OptionsTypes::AUDIO_AUDIO_TYPE)->first()->id;

        $accept_criteria = [
            'due_date'  =>  4,
            'description' => 'Audio description',
            'audio_type'  =>  $audioType,
        ];

        $resource = Resource::whereSlug(LearningResourcesEnums::Audio)->first();

        return $this->state([
            'accept_criteria' => json_encode($accept_criteria),
            'resource_id' => $resource->id,
            'resource_slug' =>  $resource->slug,
            'subject_format_subject_id' => SubjectFormatSubject::factory()->create()->id,
            'is_active' => 1,
            'is_editable' => 1,
            'list_order_key' => 1
        ]);
    }

    public function flash()
    {
        $accept_criteria = [
            'due_date'  =>  4,
            'description' => 'Flash description',
        ];

        $resource = Resource::whereSlug(LearningResourcesEnums::FLASH)->first();

        return $this->state([
            'accept_criteria' => json_encode($accept_criteria),
            'resource_id' => $resource->id,
            'resource_slug' =>  $resource->slug,
            'subject_format_subject_id' => SubjectFormatSubject::factory()->create()->id,
            'is_active' => 1,
            'is_editable' => 1,
            'list_order_key' => 1
        ]);
    }

    public function drag_drop()
    {
        $difficulty_level = Option::whereType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->inRandomOrder()->first()->id;

        $learning_outcome = Option::whereType(OptionsTypes::RESOURCE_LEARNING_OUTCOME)->inRandomOrder()->first()->id;

        $dragDropType = Option::whereType(OptionsTypes::DRAG_DROP_DRAG_DROP_TYPE)->first()->id;

        $accept_criteria = [
            'due_date'  =>  4,
            'description' => 'drag & drop description',
            'number_of_question'    =>  10,
            'difficulty_level'  =>  $difficulty_level,
            'learning_outcome'  =>  $learning_outcome,
            'drag_drop_type'  =>  $dragDropType,
        ];

        $resource = Resource::whereSlug(LearningResourcesEnums::DRAG_DROP)->first();

        return $this->state([
            'accept_criteria' => json_encode($accept_criteria),
            'resource_id' => $resource->id,
            'resource_slug' =>  $resource->slug,
            'subject_format_subject_id' => SubjectFormatSubject::factory()->create()->id,
            'is_active' => 1,
            'is_editable' => 1,
            'list_order_key' => 1
        ]);
    }

    public function pdf()
    {
        $pdfType = Option::whereType(OptionsTypes::PDF_PDF_TYPE)->first()->id;

        $accept_criteria = [
            'due_date'  =>  4,
            'description' => 'PDF description',
            'pdf_type'  =>  $pdfType,
        ];

        $resource = Resource::whereSlug(LearningResourcesEnums::PDF)->first();

        return $this->state([
            'accept_criteria' => json_encode($accept_criteria),
            'resource_id' => $resource->id,
            'resource_slug' =>  $resource->slug,
            'subject_format_subject_id' => SubjectFormatSubject::factory()->create()->id,
            'is_active' => 1,
            'is_editable' => 1,
            'list_order_key' => 1
        ]);
    }

    public function picture()
    {
        $accept_criteria = [
            'due_date'  =>  4,
            'description' => 'Picture description',
        ];

        $resource = Resource::whereSlug(LearningResourcesEnums::PICTURE)->first();

        return $this->state([
            'accept_criteria' => json_encode($accept_criteria),
            'resource_id' => $resource->id,
            'resource_slug' =>  $resource->slug,
            'subject_format_subject_id' => SubjectFormatSubject::factory()->create()->id,
            'is_active' => 1,
            'is_editable' => 1,
            'list_order_key' => 1
        ]);
    }

    public function matching()
    {
        $difficulty_level = Option::whereType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->inRandomOrder()->first()->id;

        $learning_outcome = Option::whereType(OptionsTypes::RESOURCE_LEARNING_OUTCOME)->inRandomOrder()->first()->id;

        $accept_criteria = [
            'due_date'  =>  4,
            'description' => 'matching description',
            'number_of_question'    =>  10,
            'difficulty_level'  =>  $difficulty_level,
            'learning_outcome'  =>  $learning_outcome,
        ];

        $resource = Resource::whereSlug(LearningResourcesEnums::MATCHING)->first();

        return $this->state([
            'accept_criteria' => json_encode($accept_criteria),
            'resource_id' => $resource->id,
            'resource_slug' =>  $resource->slug,
            'subject_format_subject_id' => SubjectFormatSubject::factory()->create()->id,
            'is_active' => 1,
            'is_editable' => 1,
            'list_order_key' => 1
        ]);
    }

    public function multiple_matching()
    {
        $difficulty_level = Option::whereType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->inRandomOrder()->first()->id;

        $learning_outcome = Option::whereType(OptionsTypes::RESOURCE_LEARNING_OUTCOME)->inRandomOrder()->first()->id;

        $accept_criteria = [
            'due_date'  =>  4,
            'description' => 'multi matching description',
            'number_of_question'    =>  10,
            'difficulty_level'  =>  $difficulty_level,
            'learning_outcome'  =>  $learning_outcome,
        ];

        $resource = Resource::whereSlug(LearningResourcesEnums::MULTIPLE_MATCHING)->first();

        return $this->state([
            'accept_criteria' => json_encode($accept_criteria),
            'resource_id' => $resource->id,
            'resource_slug' =>  $resource->slug,
            'subject_format_subject_id' => SubjectFormatSubject::factory()->create()->id,
            'is_active' => 1,
            'is_editable' => 1,
            'list_order_key' => 1
        ]);
    }

    public function page()
    {
        $accept_criteria = [
            'due_date'  =>  4,
            'description' => 'Page description',
        ];

        $resource = Resource::whereSlug(LearningResourcesEnums::PAGE)->first();

        return $this->state([
            'accept_criteria' => json_encode($accept_criteria),
            'resource_id' => $resource->id,
            'resource_slug' =>  $resource->slug,
            'subject_format_subject_id' => SubjectFormatSubject::factory()->create()->id,
            'is_active' => 1,
            'is_editable' => 1,
            'list_order_key' => 1
        ]);
    }

    public function hotspot()
    {
        $difficulty_level = Option::whereType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->inRandomOrder()->first()->id;

        $learning_outcome = Option::whereType(OptionsTypes::RESOURCE_LEARNING_OUTCOME)->inRandomOrder()->first()->id;

        $accept_criteria = [
            'due_date'  =>  4,
            'description' => 'hotspot description',
            'number_of_question'    =>  10,
            'difficulty_level'  =>  $difficulty_level,
            'learning_outcome'  =>  $learning_outcome,
        ];

        $resource = Resource::whereSlug(LearningResourcesEnums::HOTSPOT)->first();

        return $this->state([
            'accept_criteria' => json_encode($accept_criteria),
            'resource_id' => $resource->id,
            'resource_slug' =>  $resource->slug,
            'subject_format_subject_id' => SubjectFormatSubject::factory()->create()->id,
            'is_active' => 1,
            'is_editable' => 1,
            'list_order_key' => 1
        ]);
    }

    public function complete()
    {
        $difficulty_level = Option::whereType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->inRandomOrder()->first()->id;

        $learning_outcome = Option::whereType(OptionsTypes::RESOURCE_LEARNING_OUTCOME)->inRandomOrder()->first()->id;

        $accept_criteria = [
            'due_date'  =>  4,
            'description' => 'complete description',
            'number_of_question'    =>  10,
            'difficulty_level'  =>  $difficulty_level,
            'learning_outcome'  =>  $learning_outcome,
        ];

        $resource = Resource::whereSlug(LearningResourcesEnums::COMPLETE)->first();

        return $this->state([
            'accept_criteria' => json_encode($accept_criteria),
            'resource_id' => $resource->id,
            'resource_slug' =>  $resource->slug,
            'subject_format_subject_id' => SubjectFormatSubject::factory()->create()->id,
            'is_active' => 1,
            'is_editable' => 1,
            'list_order_key' => 1
        ]);
    }
}
