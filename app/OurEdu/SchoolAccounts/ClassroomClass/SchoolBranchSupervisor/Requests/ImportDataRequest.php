<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Requests;


use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use Exception;
use Maatwebsite\Excel\HeadingRowImport;

class ImportDataRequest extends BaseAppRequest
{
    public function rules()
    {
        return [
            "excel-data" => ["required",
                function ($attribute, $value, $fail) {
                    $originalFile = (new HeadingRowImport)->toArray(public_path() . '/classes-import.xlsx');
                    $importedFile = (new HeadingRowImport)->toArray($value);

                    if (!$this->isValidHeader($originalFile, $importedFile)) {
                        $fail(trans('classroomClassSession.Your File does not match the template file'));
                    }
                },
            ],
        ];
    }

    /**
     * @param array $originalHeader
     * @param array $uploadedHeader
     * @return bool
     */
    private function isValidHeader(array $originalHeader, array $uploadedHeader)
    {
        try {
            return array_intersect(array_slice($originalHeader[0][0],0,14), $uploadedHeader[0][0]) == array_slice($originalHeader[0][0], 0, 14);
        } catch (Exception$exception) {
            return false;
        }
    }
}



