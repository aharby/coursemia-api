<?php


namespace App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Transformers;

use App\OurEdu\BaseApp\Enums\S3Enums;
use App\OurEdu\SchoolAccounts\SessionPreparations\Models\PreparationMedia;
use League\Fractal\TransformerAbstract;

class PreparationMediaTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
    ];


    /**
     * @param PreparationMedia $preparationMedia
     * @return array
     */
    public function transform(PreparationMedia $preparationMedia)
    {
        return [
            'id' => (int)$preparationMedia->id,
            'classroom_id' => (int)$preparationMedia->sessionPreparation->classroom->id ?? 0,
            'branch_id' => (int)$preparationMedia->sessionPreparation->classroom->branch_id ?? 0,
            'grade_class' => (int)$preparationMedia->sessionPreparation->classroom->branchEducationalSystemGradeClass->gradeClass->id ?? 0,
            'subject_id' => (int)$preparationMedia->subject_id,
            'mime_type' => (string) $preparationMedia->mime_type,
            'file_name' => (string) $preparationMedia->source_filename,
            'name' => (string) ($preparationMedia->name  ??  $preparationMedia->source_filename),
            'url' =>  (string)(getImagePath(S3Enums::LARGE_PATH . $preparationMedia->filename)),
            'extension' => (string)$preparationMedia->extension
        ];
    }
}
