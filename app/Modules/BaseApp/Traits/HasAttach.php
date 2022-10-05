<?php

namespace App\Modules\BaseApp\Traits;

use App\Modules\BaseApp\Enums\S3Enums;
use File;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

trait HasAttach
{

    public static function bootHasAttach()
    {
        static::saved(function ($model) {
            $model->autoUpload();
        });
        static::deleted(function ($model) {
            $model->autoDelete();
        });
    }

    protected function autoUpload()
    {
        if (isset(static::$attachFields)) {
            $fields = static::$attachFields;
            foreach ($fields as $field => $value) {
                if (isset($value['rules'])) {
                    $this->validateImage(request()->file($field), $field, $value['rules']);
                }
                $oldFile = $this->getOriginal($field);
                //////////////////// upload
                if (request()->hasFile($field) && request()->file($field)->isValid()) {
                    $uploadPath = (@$value['path']) ?: 'uploads';
//                    if (!\Illuminate\Support\Facades\File::exists($uploadPath)) {
//                        \Illuminate\Support\Facades\File::chmod($uploadPath, 0777);
//                        \Illuminate\Support\Facades\File::makeDirectory($uploadPath);
//                    }
                    $image = request()->file($field);
                    $fileName = strtolower(Str::random(10)) . time() . '.' . $image->getClientOriginalExtension();
                    Storage::putFileAs($uploadPath, $image,$fileName);
//                    request()->file($field)->move($uploadPath, $fileName);
                    $filePath = $uploadPath . '/' . $fileName;
                    /////////////////////// resize
                    if ($filePath) {
                        $imageSizes = @$value['sizes'];
                        if ($imageSizes) {
                            foreach ($imageSizes as $key => $value) {
                                $value = explode(',', $value);
                                $type = $value[0];
                                $dimensions = explode('x', $value[1]);
//                                if (!File::exists($uploadPath . '/' . $key)) {
//                                    @mkdir($uploadPath . '/' . $key);
//                                    @chmod($uploadPath . '/' . $key, 0777);
//                                }
                                $thumbPath = $uploadPath . '/' . $key . '/' . $fileName;
                                $modulePath = substr(strstr($key,"/"), 1);
                                if (!Storage::exists($thumbPath)){
                                    reSizeImage($filePath,$dimensions[0],$dimensions[1],
                                        $image->getClientOriginalExtension(),$thumbPath,$type);
                                }
//                                $image = Image::make($filePath);
//                                if ($type == 'crop') {
//                                    $image->fit($dimensions[0], $dimensions[1]);
//                                } else {
//                                    $image->resize($dimensions[0], $dimensions[1], function ($constraint) {
//                                        $constraint->aspectRatio();
//                                    });
//                                }
//                                $image->save($thumbPath);
                            }
                            deleteImagePath($filePath);
////                            @unlink($filePath);
                        }
                    }
                    ///////////////////////////////////// Delete Old file
                    if ($oldFile) {
                        $this->deleteFile($oldFile, $uploadPath);
                    }
                    //////////////////////////////////// Update Model
                    $this->updateModel($field, $modulePath.'/'.$fileName);
                }
            }
        }
    }

    protected function validateImage($file, $fieldName, $rules)
    {
        if ($rules) {
//            $validator = Validator::make([$fieldName => $file], [$fieldName => $rules]);
//            if ($validator->fails()) {
//                dd('failed');
//                return back();
//            }
//            else{
//                dd('succeded');
//            }
            $this->validationFactory()->make(
                [$fieldName => $file], [$fieldName => $rules]
            )->validate();
        }
    }

    protected function validationFactory()
    {
        return app(Factory::class);
    }

    protected function deleteFile($file_name = "", $path = "app/public/uploads")
    {
        $path = $path . '/';
        if (!@$file_name) {
            return false;
        }
//        $directories = $this->exploreDirectory($path);
//        if ($directories) {
//            if (file_exists($path . $file_name)) {
//
//                @unlink($path . $file_name);
//            }
//            foreach ($directories as $dir) {
//                if (file_exists($path . $dir . '/' . $file_name)) {
//                    @unlink($path . $dir . '/' . $file_name);
//                }
//            }
//        }
        deleteImagePath($path.$file_name);
    }

    private function exploreDirectory($dirPath)
    {
        if ($handle = @opendir($dirPath)) {
            while (false !== ($file = @readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (@is_dir("$dirPath/$file")) {
                        $arr[] = "$file";
                    }
                }
            }
            closedir($handle);
        }
        if (@$arr) {
            return $arr;
        }
    }

    protected function updateModel($field, $fileName)
    {
        $this->attributes[$field] = $fileName;
        $dispatcher = $this->getEventDispatcher();
        self::unsetEventDispatcher();
        $this->save();
        self::setEventDispatcher($dispatcher);
    }

    protected function autoDelete()
    {
//        if ($fields) {
//            foreach ($fields as $field => $value){
//
//            }
//        }
    }

}
