<?php

namespace App\OurEdu\SchoolAccounts\SchoolRequests\Controllers;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\SchoolAccounts\SchoolRequests\Repository\SchoolRequestRepository;
use App\OurEdu\SchoolAccounts\SchoolRequests\Requests\Api\RequestSchoolRequest;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class SchoolRequestController extends BaseApiController
{
    private $repository;
    /**
     * @var ParserInterface
     */
    private $pareserInterface;

    public function __construct(SchoolRequestRepository $repository, ParserInterface $parserInterface)
    {
        $this->repository = $repository;
        $this->pareserInterface = $parserInterface;

    }

    public function addRequest(RequestSchoolRequest $request)
    {
        try {
            $data = $request->getContent();
            $data = $this->pareserInterface->deserialize($data);
            $data = $data->getData();
            $requestData = [
                'school_name' => $data->school_name,
                'number_of_students' => $data->number_of_students,
                'manager_name' => $data->manager_name,
                'manager_mobile' => $data->manager_mobile,
                'manager_email' => $data->manager_email
            ];
            $this->repository->create($requestData);
            return response()->json([
                'meta' => [
                    'message' => trans('app.Added successfully')
                ]
            ]);
        }catch (\Exception $exception){
            Log::error($exception);
            throw new OurEduErrorException($exception->getMessage());
        }

    }
}
