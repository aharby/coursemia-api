<?php


namespace App\OurEdu\StaticPages\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\StaticBlocks\Transformers\StaticBlockTransformer;
use App\OurEdu\StaticPages\Models\DistinguishedStudent;
use App\OurEdu\StaticPages\Repository\DistinguishedStudentsRepository;
use App\OurEdu\StaticPages\StaticPage;
use App\OurEdu\StaticPages\Transformers\DistinguishedStudents\DistinguishedStudentsListTransformer;
use League\Fractal\TransformerAbstract;

class StaticPageTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
        'blocks',
        'distinguishedStudents'
    ];


    public function transform(StaticPage $staticPage)
    {
        return [
            'id' => $staticPage->id,
            'slug' => $staticPage->slug,
            'url' => $staticPage->url,
            'bg_image' => $staticPage->bg_image,
            'is_active' => $staticPage->is_active,
            'title' => $staticPage->title,
            'body' => $staticPage->body,
        ];
    }


    public function includeBlocks(StaticPage $staticPage)
    {
        $staticBlocks = $staticPage->staticBlocks;
        if ($staticBlocks) {
            return $this->Collection($staticBlocks, new StaticBlockTransformer(), ResourceTypesEnums::STATIC_BLOCK);
        }
    }

    public function includeDistinguishedStudents()
    {
        $distinguishedStudentsRepo = new DistinguishedStudentsRepository(new DistinguishedStudent);
        $students = $distinguishedStudentsRepo->listDistinguishedStudentsInDays();
        return $this->Collection($students, new DistinguishedStudentsListTransformer(), ResourceTypesEnums::DISTINGUISHED_STUDENTS);
    }
}

