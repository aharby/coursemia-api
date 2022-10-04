<?php


namespace App\OurEdu\SchoolAccounts\SessionPreparations\Student\Transformers;


use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\SchoolAccounts\SessionPreparations\Models\SessionPreparation;
use League\Fractal\TransformerAbstract;

class SessionPreparationsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [];
    protected array $availableIncludes = [
        "preparation_media",
        "section"
    ];


    /**
     * @param SessionPreparation $sessionPreparation
     * @return array
     */
    public function transform(SessionPreparation $sessionPreparation)
    {
        return [
            "id" => (int)$sessionPreparation->id,
            'classroom_id' => (int)$sessionPreparation->classroom_id,
            'subject_id' => (int)$sessionPreparation->subject_id,
            'section_id' => $sessionPreparation->section_id,
            'objectives' => (string)$sessionPreparation->objectives ?? "",
            'pre_Learning' => (string)$sessionPreparation->pre_Learning ?? "",
            'introductory' => (string)$sessionPreparation->introductory ?? "",
            'application' => (string)$sessionPreparation->application ?? "",
            'evaluation' => (string)$sessionPreparation->evaluation ?? "",
            'internal_preparation' => (string)$sessionPreparation->internal_preparation,
        ];
    }

    public function includePreparationMedia(SessionPreparation $sessionPreparation)
    {
        $preparationMedia = $sessionPreparation->media;

        return $this->collection(
            $preparationMedia,
            new PreparationMediaTransformer(),
            ResourceTypesEnums::PREPARATION_MEDIA
        );
    }

    public function includeSection(SessionPreparation $sessionPreparation)
    {
        $section = $sessionPreparation->section;

        if ($section) {
            return $this->item($section, new SectionTransformer(), ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT);
        }
    }
}
