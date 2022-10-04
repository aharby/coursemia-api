<?php

namespace App\OurEdu\BaseApp\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Interfaces\DataInterface;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use Swis\JsonApi\Client\Item;

class BaseApiParserRequest extends FormRequest
{
    public DataInterface $data;
    private ParserInterface $parserInterface;

    public function __construct(ParserInterface $parserInterface)
    {
        parent::__construct();
        $this->parserInterface = $parserInterface;
    }

    public function validationData(): array
    {
        if ($this->getContent() != "") {
            $data = $this->parserInterface->deserialize($this->getContent());
            $data = $data->getData();
            $this->data = $data;
            if ($data instanceof Collection) {
                $data = $data->first();
            }
            return $data->toJsonApiArray();
        }
        $this->data = new Item();
        return [];
    }

    public function getParser(): ParserInterface
    {
        return $this->parserInterface;
    }

    public function getData(): DataInterface
    {
        return $this->data;
    }

    public function rules()
    {
        return [];
    }
}
