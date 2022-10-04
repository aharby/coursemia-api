<?php


namespace App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\SchoolAccounts\SessionPreparations\Models\SessionPreparation;
use League\Fractal\TransformerAbstract;

class SessionPreparationTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
        'media'
    ];


    /**
     * @param SessionPreparation $sessionPreparation
     * @return array
     */
    public function transform(SessionPreparation $sessionPreparation)
    {
        return [
            'id' => (int)$sessionPreparation->id,
            'classroom_id' => (int)$sessionPreparation->classroom_id,
            'subject_id' => (int)$sessionPreparation->subject_id,
            'internal_preparation' => (string)$sessionPreparation->internal_preparation,
            'pre_Learning' => (string)$sessionPreparation->pre_Learning,
            'introductory' => (string)$sessionPreparation->introductory,
            'evaluation' => (string)$sessionPreparation->evaluation,
            'application' => (string)$sessionPreparation->application,
            'section_id' => $sessionPreparation->section_id,
            'objectives' => (string)$sessionPreparation->objectives,
            'section_title' => $sessionPreparation->section ? $sessionPreparation->section->title : $sessionPreparation->objectives,
            'parent_section_id' => $sessionPreparation->section ? $sessionPreparation->section->parent_subject_format_id : null,
            "is_published" => (bool)isset($sessionPreparation->published_at),
        ];
    }

    public function includeMedia(SessionPreparation $sessionPreparation)
    {
        return $this->collection(
            $sessionPreparation->media,
            new PreparationMediaTransformer(),
            ResourceTypesEnums::PREPARATION_MEDIA
        );
    }
}
