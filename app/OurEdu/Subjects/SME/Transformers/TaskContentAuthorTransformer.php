<?php

namespace App\OurEdu\Subjects\SME\Transformers;

use League\Fractal\TransformerAbstract;
use App\OurEdu\Users\Models\ContentAuthor;


class TaskContentAuthorTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(ContentAuthor $author)
    {
        return [
            'id' => (int) $author->id,
            'name' => (string) $author->user->name,
        ];
    }
}
