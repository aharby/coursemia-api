<?php

namespace App\OurEdu\Contact\Controllers;



use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\Contact\Repository\ContactInterface;
use App\OurEdu\Contact\Requests\ContactApiRequest;

class ContactApiController extends BaseApiController
{
    private $contactRepo;

    public function __construct(ContactInterface $contactRepo)
    {
        $this->contactRepo = $contactRepo;
    }

    public function postCreate(ContactApiRequest $request) {
        $this->contactRepo->create($request->getData()->toJsonApiArray()['attributes']);

        return response()->json(
            [
                "meta" => [
                    'message' => trans('api.Contact Sent Successfully')
                ]
            ]
        );
    }

}

