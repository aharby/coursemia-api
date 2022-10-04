<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\UseCases\Imports;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class UploaderUseCase
{
    /**
     * @param UploadedFile $file
     * @param $folder
     * @return string
     */
    public function upload(UploadedFile $file, $folder) : ?string
    {
        $path = storage_path('app/public') . '/uploads/'.$folder;

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true);
        }

        $file_name = date('YmdHis') . mt_rand() . '_'.$folder.'.' . $file->getClientOriginalExtension();

        if ($file->move($path, $file_name)) {
            return $img = '/uploads/'.$folder.'/' . $file_name;
        }
    }

}
