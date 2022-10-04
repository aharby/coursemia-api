<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\ContentAuthor;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\Producers\Subject\SubjectCreated;
use App\Producers\Subject\SubjectUpdated;

class SyncSubjectsWithOurEduSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subjects = Subject::where('is_active',1)->whereHas('educationalSystem.translations')
        ->whereHas('educationalTerm.translations')
        ->whereHas('academicalYears.translations')
        ->whereHas('gradeClass.translations')
        ->where('country_id', env('fall_back_country_id'))
        ->get();
        foreach($subjects as $subject){
            $this->publishSubjectToOurEdu($subject);
        }
    }

    private function publishSubjectToOurEdu(Subject $subject)
    {
        $ourEduReference = $subject->our_edu_reference;

        $payload = [
            'our_edu_reference' => $ourEduReference,
            'name_en' => $subject->name,
            'name_ar' => $subject->name,
            'ta3lom_reference' => $subject->id,
            'educational_system' => [
                'our_edu_reference' => $subject->educationalSystem->our_edu_reference,
                'name_en' => $subject->educationalSystem->translate('en')->name,
                'name_ar' => $subject->educationalSystem->translate('ar')->name,
                'ta3lom_reference' => $subject->educationalSystem->id
            ],
            'semester' => [
                'our_edu_reference' => $subject->educationalTerm->our_edu_reference,
                'name_en' => $subject->educationalTerm->translate('en')->title,
                'name_ar' => $subject->educationalTerm->translate('ar')->title,
                'ta3lom_reference' => $subject->educationalTerm->id
            ],
            'academic_year' => [
                'our_edu_reference' => $subject->academicalYears->our_edu_reference,
                'name_en' => $subject->academicalYears->translate('en')->title,
                'name_ar' => $subject->academicalYears->translate('ar')->title,
                'ta3lom_reference' => $subject->academicalYears->id
            ],
            'grade' => [
                'our_edu_reference' => $subject->gradeClass->our_edu_reference,
                'name_en' => $subject->gradeClass->translate('en')->title,
                'name_ar' => $subject->gradeClass->translate('ar')->title,
                'ta3lom_reference' => $subject->gradeClass->id
            ]
        ];

        if (!is_null($ourEduReference)) {
            SubjectUpdated::publish($payload);
        } else {
            SubjectCreated::publish($payload);
        }
    }
}
