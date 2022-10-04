<?php


namespace App\OurEdu\LearningResources\Transformers;


use App\OurEdu\LearningResources\Helpers\ResourceOptions;
use App\OurEdu\LearningResources\Resource;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class LearningResourceAcceptCriteriaTransformer extends TransformerAbstract
{
    protected $learningResource;

    protected array $defaultIncludes = [
    ];

    public function __construct($learningResource = '')
    {
        $this->learningResource = $learningResource;
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    /**
     * @param Resource $resource
     * @return array
     */
    public function transform($resource)
    {


        $resourceOptions = new ResourceOptions();


        $data = [
            'id' => Str::uuid(),


        ];
        foreach ($resource as $key => $value) {
            $keyAttribute = [];
            $keyAttribute['initial_value'] = $value['initial_value'] ?? '';
            $keyAttribute['data_type'] = $value['data_type'] ?? null;
            $keyAttribute['have_options'] = $value['have_options'] ?? false;


            $keyAttribute['validation'] = $this->mapValidationToFront( $value['validation']);


            $resourceOptionsFunctionName = Str::camel($this->learningResource . '_' . $key . 'Options');


            if (method_exists($resourceOptions, $resourceOptionsFunctionName)) {
                $options = call_user_func_array(array($resourceOptions, $resourceOptionsFunctionName), [$key]);
                $keyAttribute['options'] = $options;

            }
            $data[$key] = $keyAttribute;


        }


        return $data;
    }


    function mapValidationToFront($validation)
    {
        $validations = explode('|', $validation);

        $frontValidation = [];
        foreach ($validations as $validation) {

            if (Str::contains($validation, ':')) {
                $valid = explode(':', $validation);
                $frontValidation[$valid[0]] = $valid[1];

            }
            switch ($validation) {

                case 'required':
                    $frontValidation[$validation] = true;
                    break;
                case 'numeric':
                    $frontValidation['number'] = true;
                    break;

            }
        }
        return (count($frontValidation) > 0) ? $frontValidation : (object)[];

    }
}

