<?php


namespace App\OurEdu\SchoolAccounts\SchoolAccounts\Transformers;


use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use League\Fractal\TransformerAbstract;

class SchoolAccountTransformer extends TransformerAbstract
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
    public function transform(SchoolAccount $schoolAccount)
    {
        $transformedData = [
            'id' => $schoolAccount->id,
            'school_name' => $schoolAccount->name,
        ];
        return $transformedData;
    }

}
