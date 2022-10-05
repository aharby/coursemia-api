<?php


namespace App\Modules\StaticPages\Transformers;

use App\Modules\BaseApp\Enums\ResourceTypesEnums;
use App\Modules\StaticBlocks\Transformers\StaticBlockTransformer;
use App\Modules\StaticPages\Models\DistinguishedStudent;
use App\Modules\StaticPages\Repository\DistinguishedStudentsRepository;
use App\Modules\StaticPages\StaticPage;
use App\Modules\StaticPages\Transformers\DistinguishedStudents\DistinguishedStudentsListTransformer;
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

