<?php

use App\Modules\BaseApp\Api\AgoraHandlerV2;
use App\Modules\BaseApp\Enums\DynamicLinksEnum;
use App\Modules\BaseApp\Enums\S3Enums;
use App\Modules\Config\Config;
use App\Modules\Courses\Enums\CourseEnums;
use App\Modules\Courses\Models\Course;
use App\Modules\Exams\Models\Competitions\CompetitionQuestionStudent;
use App\Modules\GarbageMedia\MediaEnums;
use App\Modules\LearningResources\Enums\LearningResourcesEnums;
use App\Modules\Options\Option;
use App\Modules\ResourceSubjectFormats\Models\Audio\AudioData;
use App\Modules\ResourceSubjectFormats\Models\Complete\CompleteData;
use App\Modules\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\Modules\ResourceSubjectFormats\Models\Flash\FlashData;
use App\Modules\ResourceSubjectFormats\Models\HotSpot\HotSpotMedia;
use App\Modules\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\Modules\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\Modules\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use App\Modules\ResourceSubjectFormats\Models\Page\PageData;
use App\Modules\ResourceSubjectFormats\Models\Pdf\PdfData;
use App\Modules\ResourceSubjectFormats\Models\Picture\PictureData;
use App\Modules\ResourceSubjectFormats\Models\Progress\ResourceProgressStudent;
use App\Modules\ResourceSubjectFormats\Models\Progress\SubjectFormatProgressStudent;
use App\Modules\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\Modules\ResourceSubjectFormats\Models\Video\VideoData;
use App\Modules\SubjectPackages\Package;
use App\Modules\Subjects\Models\SubModels\SubjectFormatSubject;
use App\Modules\Subjects\Models\SubModels\SubjectTime;
use App\Modules\Users\Auth\Enum\TokenNameEnum;
use App\Modules\Users\Auth\TokenManager\TokenManagerInterface;
use App\Modules\Users\UserEnums;
use App\Modules\VCRSchedules\Models\VCRRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Modules\Exams\Enums\ExamTypes;
use App\Modules\ExternalServices\ArabicHandling\Arabic\I18N\Arabic\I18N_Arabic_Numbers;
use App\Modules\ResourceSubjectFormats\Enums\ResourcesConfigurationEnum;
use App\Modules\Subjects\Models\Subject;
use Carbon\Carbon;


if (!function_exists('urlLang')) {
    function urlLang($url, $fromlang, $toLang)
    {
        $currentUrl = str_replace('/' . $fromlang . '/', '/' . $toLang . '/', strtolower($url));
        return $currentUrl;
    }
}

if (!function_exists('getConfigs')) {
    function getConfigs()
    {
        if (\Cache::has('configs')) {
            return \Cache::get('configs');
        } else {
            updateConfigsCache();
        }
    }
}

if (!function_exists('updateConfigsCache')) {
    function updateConfigsCache()
    {
        if (\Schema::hasTable('configs')) {
            $configs = Config::get();
            $arr = [];
            if ($configs) {
                foreach ($configs as $c) {
                    $key = $c->field;
                    $arr[$key][$c->lang] = $c->value;
                }
            }
            Cache::put('configs', $arr, env('CACHE_TIME', now()->addSeconds(24 * 60 * 60)));
        }
    }

}

if (!function_exists('getToken')) {
    function getToken($appid, $appcertificate, $account, $validTimeInSeconds){
        $SDK_VERSION = "1";
        $expiredTime = time() + $validTimeInSeconds;

        $token_items = array();
        array_push($token_items, $SDK_VERSION);
        array_push($token_items, $appid);
        array_push($token_items, $expiredTime);
        array_push($token_items, md5($account.$appid.$appcertificate.$expiredTime));
        return join(":", $token_items);
    }
}


if (!function_exists('appName')) {
    function appName()
    {
        $configs = getConfigs();
        $appName = (@$configs['application_name'][lang()]) ?: env('APP_NAME');
        return $appName;
    }
}


if (!function_exists('getListOfFiles')) {
    function getListOfFiles($path)
    {
        $out = [];
        $results = scandir($path);
        foreach ($results as $result) {
            if ($result === '.' or $result === '..') {
                continue;
            }
            if (is_dir($result)) {
                $out = array_merge($out, getListOfModel($path . '/' . $result));
            } else {
                $out[] = substr($result, 0, -4);
            }
        }
        return $out;
    }
}

if (!function_exists('generateImage')) {
    function generateImage($text)
    {
        $url = 'https://ui-avatars.com/api/?name=' . $text . '&size=100';
        $contents = @file_get_contents($url);
        if ($contents) {
            $filename = strtolower(str_random(10)) . time() . '.png';
            @file_put_contents(public_path() . '/uploads/small/' . $filename, $contents);
            return $filename;
        }
    }
}

if (!function_exists('export')) {
    function export($data, $labels, $module)
    {
        \Maatwebsite\Excel\Facades\Excel::create(
            $module . "_" . date("Y-m-d H:i:s"),
            function ($excel) use ($data, $labels) {
                $excel->sheet('Sheetname', function ($sheet) use ($data, $labels) {
                    $sheet->row(1, $labels);
                    $sheet->rows($data);
                    $sheet->row(1, function ($row) {
                        // call cell manipulation methods
                        $row->setFontWeight('bold');
                    });
                });
            }
        )->export('xls');
    }
}

if (!function_exists('encodeRequest')) {
    function encodeRequest($request)
    {
        $array = [];
        foreach ($request as $k => $r) {
            if (is_array($r)) {
                $array[$k] = json_encode($r);
            } else {
                $array[$k] = $r;
            }
        }
        return $array;
    }
}


if (!function_exists('authorize')) {
    function authorize($action)
    {
        if (is_array($action)) {
            foreach ($action as $ac) {
                if (!can($action)) {
                    return abort(403, 'Unauthorized action.');
                }
            }
        }

        if (!can($action)) {
            return abort(403, 'Unauthorized action.');
        }
    }

}

if (!function_exists('can')) {
    function can($action)
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        if ($user->type == UserEnums::SCHOOL_ACCOUNT_MANAGER || $user->type == UserEnums::SCHOOL_ADMIN) {
            return true;
        }
        if ($user->super_admin) {
            return true;
        }
        if (!$user->role_id) {
            return false;
        }
        if (!$user->role->permissions) {
            return false;
        }
        if (!in_array($action, $user->role->permissions)) {
            return false;
        }
        return true;
    }
}

if (!function_exists('canWithMultipleAction')) {
    function canWithMultipleAction($actionArr)
    {
        $user = auth()->user();
        $data = [];
        if (!$user) {
            if (!request()->header('token')) {
                return false;
            }
            $user = \App\Starter\Users\User::active()->first();
        }
        if ($actionArr) {
            foreach ($actionArr as $key => $action) {
                if ($user || $user->role_id) {
                    if ($user->super_admin || in_array($action, $user->role->permissions)) {
                        $data[$action] = true;
                    }
                } else {
                    $data[$action] = false;
                }
            }
            return $data;
        }
        return true;
    }
}

if (!function_exists('checkAllActions')) {
    function checkAllActions($actionArr)
    {
        $user = auth()->user();
        if ($actionArr) {
            foreach ($actionArr as $key => $action) {
                if ($user || $user->role_id) {
                    if ($user->super_admin || in_array($action, $user->role->permissions)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}

if (!function_exists('getDefaultLang')) {
    function getDefaultLang()
    {
        if (in_array(request()->segment(1), langs())) {
            return LaravelLocalization::setLocale(request()->segment(1));
        } else {
            if (request()->segment(1) == '') {
                LaravelLocalization::setLocale(lang());
                return LaravelLocalization::setLocale(lang());
            } else {
                return LaravelLocalization::setLocale();
            }
        }
    }
}

if (!function_exists('lang')) {
    function lang()
    {
        return \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
    }
}

if (!function_exists('langs')) {
    function langs()
    {
        $languages = (array_keys(config('laravellocalization.supportedLocales'))) ?: [];
        return $languages;
    }
}

if (!function_exists('randString')) {
    function randString($length = 5)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randstring .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randstring;
    }
}

if (!function_exists('languages')) {
    function languages()
    {
        $languages = config('laravellocalization.supportedLocales');
        $langs = [];
        foreach ($languages as $key => $value) {
            $langs[$key] = $value['name'];
        }
        return $langs;
    }
}

if (!function_exists('transformValidation')) {
    function transformValidation($errors)
    {
        $temp = [];
        if ($errors) {
            foreach ($errors as $key => $value) {
                $temp[$key] = @$value[0];
            }
        }
        return $temp;
    }
}

if (!function_exists('image')) {
    function image($img, $type, $folder = 'uploads')
    {
        $path =$folder;
        if ($type != "") {
            $path .= "/".$type;
        }
        $path .= "/".$img;
        return getImagePath($path);
    }
}

if (!function_exists('imageProfileApi')) {
    function imageProfileApi($img, $type = 'small')
    {
        $path = 'uploads/' . $type . '/' . $img;
        return getImagePath($path,url("/img/avatar.png"));
    }
}

if (!function_exists('resourceMediaUrl')) {
    function resourceMediaUrl($path)
    {
        return getImagePath(S3Enums::LARGE_PATH . $path);
    }
}

if (!function_exists('viewImage')) {
    function viewImage($img, $type, $folder = 'uploads', $attributes = null)
    {

        $width = 200;
        if ($attributes) {
            $width = @$attributes['width'];
            $class = @$attributes['class'];
            $id = @$attributes['id'];
        }
        $src = image($img, $type, $folder);
        return '<img  width="' . $width . '" src="' . $src . '" class="' . @$class . '" id="' . @$id . '" >';
    }
}
if (!function_exists('viewInputImage')) {
    function viewInputImage($img, $type, $folder = 'uploads', $attributes = null)
    {
        $width = 150;
        if ($attributes) {
            $width = @$attributes['width'];
            $class = @$attributes['class'];
            $id = @$attributes['id'];
        }
        $src = image($img, $type, $folder);
        return '<img  width="' . $width . '" src="' . $src . '" class="' . @$class . '" id="' . @$id . '" >';
    }
}
if (!function_exists('viewFile')) {
    function viewFile($file, $folder = 'uploads', $placeholder = null)
    {
        $path = $folder . '/' . $file;
        $path =  getImagePath($path,'');
        return '<i class="fa fa-paperclip"></i> <a href="' . $path . '" target="_blank" >' . $placeholder ?? $file . '</a>';
    }
}

if (!function_exists('slug')) {
    function slug($str, $options = array())
    {
        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $str = mb_convert_encoding((string)$str, 'UTF-8');
        $defaults = array(
            'delimiter' => '-',
            'limit' => null,
            'lowercase' => true,
            'replacements' => array(),
            'transliterate' => false,
        );
        // Merge options
        $options = array_merge($defaults, $options);
        $char_map = array(
            // Latin
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
            'Æ' => 'AE', 'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I',
            'Î' => 'I', 'Ï' => 'I',
            'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'Ö' => 'O', 'Ő' => 'O',
            'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U',
            'Ý' => 'Y', 'Þ' => 'TH',
            'ß' => 'ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
            'æ' => 'ae', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i',
            'î' => 'i', 'ï' => 'i',
            'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ő' => 'o',
            'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u',
            'ý' => 'y', 'þ' => 'th',
            'ÿ' => 'y',
            // Latin symbols
            '©' => '(c)',
            // Greek
            'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z',
            'Η' => 'H', 'Θ' => '8',
            'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3',
            'Ο' => 'O', 'Π' => 'P',
            'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X',
            'Ψ' => 'PS', 'Ω' => 'W',
            'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H',
            'Ώ' => 'W', 'Ϊ' => 'I',
            'Ϋ' => 'Y',
            'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z',
            'η' => 'h', 'θ' => '8',
            'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3',
            'ο' => 'o', 'π' => 'p',
            'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x',
            'ψ' => 'ps', 'ω' => 'w',
            'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h',
            'ώ' => 'w', 'ς' => 's',
            'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
            // Turkish
            'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
            'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
            // Russian
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'Yo', 'Ж' => 'Zh',
            'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M',
            'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F',
            'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '',
            'Э' => 'E', 'Ю' => 'Yu',
            'Я' => 'Ya',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'yo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm',
            'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f',
            'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '',
            'э' => 'e', 'ю' => 'yu',
            'я' => 'ya',
            // Ukrainian
            'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
            'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
            // Czech
            'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S',
            'Ť' => 'T', 'Ů' => 'U',
            'Ž' => 'Z',
            'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's',
            'ť' => 't', 'ů' => 'u',
            'ž' => 'z',
            // Polish
            'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o',
            'Ś' => 'S', 'Ź' => 'Z',
            'Ż' => 'Z',
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o',
            'ś' => 's', 'ź' => 'z',
            'ż' => 'z',
            // Latvian
            'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k',
            'Ļ' => 'L', 'Ņ' => 'N',
            'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
            'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k',
            'ļ' => 'l', 'ņ' => 'n',
            'š' => 's', 'ū' => 'u', 'ž' => 'z'
        );
        // Make custom replacements
        $str = preg_replace(
            array_keys($options['replacements']),
            $options['replacements'],
            $str
        );
        // Transliterate characters to ASCII
        if ($options['transliterate']) {
            $str = str_replace(array_keys($char_map), $char_map, $str);
        }
        // Replace non-alphanumeric characters with our delimiter
        $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
        // Remove duplicate delimiters
        $str = preg_replace(
            '/(' . preg_quote($options['delimiter'], '/') . '){2,}/',
            '$1',
            $str
        );
        // Truncate slug to max. characters
        $str = mb_substr(
            $str,
            0,
            ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')),
            'UTF-8'
        );
        // Remove delimiter from ends
        $str = trim($str, $options['delimiter']);

        return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
    }
}

if (!function_exists('pdf')) {
    function pdf($html, $filename)
    {
        // // or pure html
        $pdfarr = [
            'title' => $filename,
            'data' => $html, // render file blade with content html
            'header' => ['show' => false], // header content
            'footer' => ['show' => false], // Footer content
            'font' => 'aealarabiya', //  dejavusans, aefurat ,aealarabiya ,times
            'font-size' => 12, // font-size
            'text' => '', //Write
            'rtl' => (lang() == 'ar') ? true : false, //true or false
            // 'creator'=>'phpanonymous', // creator file - you can remove this key
            // 'keywords'=>'phpanonymous keywords', // keywords file - you can remove this key
            // 'subject'=>'phpanonymous subject', // subject file - you can remove this key
            'filename' => $filename . '.pdf', // filename example - invoice.pdf
            'display' => 'download', // stream , download , print
        ];
        return \PDFAnony::HTML($pdfarr);
    }
}
if (!function_exists('unauthorizeWeb')) {
    function unauthorizeWeb()
    {
            return abort(403, 'Unauthorized action.');

    }
}

if (!function_exists('unauthorize')) {
    function unauthorize()
    {
        throw new HttpResponseException(response()->json([

            "errors" => [
                [
                    'status' => 403,
                    'title' => 'unauthorized_action',
                    'detail' => trans('app.Unauthorized action')
                ]
            ]

        ], 403));
    }
}

if (!function_exists('moveGarbageMedia')) {
    function moveGarbageMedia(
        $ids,
        \Illuminate\Database\Eloquent\Relations\HasOneOrMany $relation,
        string $storagePath = null,
        array $columnNamesMap = null,
        $extraColumns = []
    )
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        if (!$columnNamesMap) {
            $columnNamesMap = [
                'source_filename' => 'source_filename',
                'filename' => 'filename',
                'extension' => 'extension',
                'mime_type' => 'mime_type'
            ];
        }
        $garbageMedias = \App\Modules\GarbageMedia\GarbageMedia::find($ids);
        foreach ($garbageMedias as $garbageMedia) {
            if ($storagePath) {
                $fileName = $storagePath . '/' . $garbageMedia->filename;
            } else {
                $fileName = $garbageMedia->filename;
            }

            $data = [
                $columnNamesMap['filename'] => $fileName,
                $columnNamesMap['source_filename'] => $garbageMedia->source_filename,
                $columnNamesMap['extension'] => $garbageMedia->extension,
                $columnNamesMap['mime_type'] => $garbageMedia->mime_type
            ];

            if (isset($extraColumns[$garbageMedia->id])) {
                foreach ($extraColumns[$garbageMedia->id] as $key => $value) {
                    $data[$key] = $value;
                }
            }

            moveImagePath(S3Enums::GARBAGE_MEDIA_PATH ,
                S3Enums::LARGE_PATH ,
                $storagePath,
                $garbageMedia->filename
            );
            // in case media file is for hotspot resource, save image width and height to db as we need it in answer calculation
            if ($relation->getModel() instanceof HotSpotMedia) {
                $imageSize = getimagesize(storage_path('uploads/large/' . $storagePath . '/' . $garbageMedia->filename));
                $data['image_width'] = $imageSize[0];
                $data['image_height'] = $imageSize[1];
            }
            $relation->create($data);
        }
        \App\Modules\GarbageMedia\GarbageMedia::whereIn('id', $ids)->delete();
    }
}

if (!function_exists('moveGarbageMediaWithColumns')) {
    function moveGarbageMediaWithColumns(
        $ids,
        \Illuminate\Database\Eloquent\Relations\HasOneOrMany $relation,
        string $storagePath = null,
        array $columnNamesMap = null,
        $extraColumns = []
    )
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        if (!$columnNamesMap) {
            $columnNamesMap = [
                'source_filename' => 'source_filename',
                'filename' => 'filename',
                'extension' => 'extension',
                'mime_type' => 'mime_type'
            ];
        }
        $garbageMedias = \App\Modules\GarbageMedia\GarbageMedia::find($ids);
        foreach ($garbageMedias as $garbageMedia) {
            if ($storagePath) {
                $fileName = $storagePath . '/' . $garbageMedia->filename;
            } else {
                $fileName = $garbageMedia->filename;
            }
            $data = [
                $columnNamesMap['filename'] => $fileName,
                $columnNamesMap['source_filename'] => $garbageMedia->source_filename,
                $columnNamesMap['extension'] => $garbageMedia->extension,
                $columnNamesMap['mime_type'] => $garbageMedia->mime_type
            ];

            if (isset($extraColumns)) {
                foreach ($extraColumns as $key => $value) {
                    $data[$key] = $value;
                }
            }
            moveImagePath(S3Enums::GARBAGE_MEDIA_PATH ,
                S3Enums::LARGE_PATH ,
                $storagePath,
                $garbageMedia->filename
            );
            // in case media file is for hotspot resource, save image width and height to db as we need it in answer calculation
            if ($relation->getModel() instanceof HotSpotMedia) {
                $imageSize = getimagesize(storage_path('uploads/large/' . $storagePath . '/' . $garbageMedia->filename));
                $data['image_width'] = $imageSize[0];
                $data['image_height'] = $imageSize[1];
            }
            $relation->create($data);
        }
        \App\Modules\GarbageMedia\GarbageMedia::whereIn('id', $ids)->delete();
    }
}

if (!function_exists('moveSingleGarbageMedia')) {
    function moveSingleGarbageMedia($id, string $storagePath = null)
    {
        $garbageMedia = \App\Modules\GarbageMedia\GarbageMedia::find($id);
        $fileName = null;
        if ($garbageMedia) {
            if ($storagePath) {
                $fileName = $storagePath . '/' . $garbageMedia->filename;
            } else {
                $fileName = $garbageMedia->filename;
            }

            if (in_array($garbageMedia->extension, getImageTypes())) {
                moveGarbageMediaSmallImage($garbageMedia, $fileName);
            }
            moveImagePath(S3Enums::GARBAGE_MEDIA_PATH ,
                S3Enums::LARGE_PATH ,
                $storagePath,
                $garbageMedia->filename
            );
        }
        if ($garbageMedia) {
            $garbageMedia->delete();
        }
        return $fileName;
    }
}

if (!function_exists('moveGarbageMediaImage')) {
    function moveGarbageMediaSmallImage($garbageMedia, $imgName)
    {
        if (Storage::exists(S3Enums::GARBAGE_MEDIA_PATH . $garbageMedia->filename))
        {
            $imageSizes = ['small' => 'resize,200x200'];
            foreach ($imageSizes as $key => $value) {
                $value = explode(',', $value);
                $type = $value[0];
                $dimensions = explode('x', $value[1]);
                reSizeImage(S3Enums::GARBAGE_MEDIA_PATH.$garbageMedia->filename,
                    $dimensions[0],$dimensions[1],$garbageMedia->extension,
                    S3Enums::SMALL_PATH.$imgName,$type);
            }
        }
    }
}

if (!function_exists('deleteMedia')) {
    function deleteMedia($ids,
                         object $model,
                         string $storagePath = null)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $mediaData = $model->whereIn('id', $ids)->get();

        foreach ($mediaData as $media) {
            if (in_array($media->extension, getImageTypes())) {
                if (is_null($storagePath)) {
                    deleteImagePath(S3Enums::LARGE_PATH. $media->filename);
                    deleteImagePath(S3Enums::SMALL_PATH. $media->filename);
                }
                deleteImagePath(S3Enums::LARGE_PATH . $storagePath . '/' . $media->filename);
                deleteImagePath(S3Enums::SMALL_PATH . $storagePath . '/' . $media->filename);
            } else {
                if (is_null($storagePath)) {
                    deleteImagePath(S3Enums::LARGE_PATH . $media->filename);
                }
                deleteImagePath(S3Enums::LARGE_PATH . $storagePath . '/' . $media->filename);
            }
        }
        $model->whereIn('id', $ids)->delete();
    }
}
if (!function_exists('getImageTypes')) {
    function getImageTypes()
    {
        return [
            'jpeg',
            'png',
            'jpg',
            'gif',
            'svg'
        ];
    }
}

if (!function_exists('buildScopeRoute')) {
    /**
     * build Scope Route
     * @param $route , $param
     * @return string
     */
    function buildScopeRoute($route, array $param = [])
    {
        $params = ['language' => lang()];
        if (count($param) > 0) {
            $params = array_merge($params, $param);
        }
        if(Route::currentRouteName()=='api.sme.subjects.post.sme.markTaskAsDone'){
            dd($params);

        }
        return route($route, $params);
    }
}

if (!function_exists('purify')) {
    /**
     * Purify in comming HTML using summernote editor
     * @param string $inputValue
     * @return string  clean HTML
     */
    function purify($inputValue)
    {
        $empty_values = ['<br>', '<p><br></p>', '<p dir="auto"><br></p>'];

        if (in_array($inputValue, $empty_values)) {
            return "";
        }

        $cleanBreaks = trim($inputValue, '<p><br></p>');

        return Purify::clean($cleanBreaks);
    }
}
if (!function_exists('formatFilter')) {
    function formatFilter($data)
    {
        $arr = [];
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $key => $value) {
                $obj = new \stdClass();
                $obj->key = $key;
                $obj->value = $value;
                $arr[] = $obj;
            }
        }
        return $arr;
    }
}
if (!function_exists('createApiActionButtons')) {
    function createApiActionButtons($actions)
    {
        $returnActions = [];

        foreach ($actions as $action) {
            $returnActions[] = [
                'endpoint_url' => $action['endpoint_url'],
                'label' => $action['label'],
                'bg_color' => $action['bg_color'] ?? '#228B22',
                'key' => $action['key'] ?? null
            ];
        }
        return $returnActions;
    }
}

if (!function_exists('checkLoginGuard')) {
    function checkLoginGuard()
    {
        if (auth('api')->check()) {
            return auth('api');
        } else {
            return auth();
        }
    }
}

if (!function_exists('formatFiltersForApi')) {
    function formatFiltersForApi($filters)
    {
        $result = [];
        foreach ($filters as $filter) {
            $filterResult = [];
            if (isset($filter['name'])) {
                $filterResult['name'] = $filter['name'];
            }
            if (isset($filter['type'])) {
                $filterResult['type'] = $filter['type'];
            }
            if (isset($filter['value'])) {
                $filterResult['value'] = $filter['value'];
            } else {
                $filterResult['value'] = null;
            }
            if (isset($filter['data'])) {
                $filterResult['data'] = formatFilter($filter['data']);
            }
            $result[] = $filterResult;
        }

        return $result;
    }
}

if (!function_exists('formatErrorValidation')) {
    /**
     *  JsonApi Error format Vlaidation
     * @param array $errors
     * @param int $code
     */
    function formatErrorValidation(array $errors, int $code = 422)
    {
        $errorsArray = [];
        foreach ($errors as $error) {
            if (is_array($error)) {
                $errorsArray[] = [
                    'status' => $error['status'],
                    'title' => snake_case($error['title']),
                    'detail' => $error['detail'],
                ];
            } else {
                $errorsArray[] = [
                    'status' => $errors['status'],
                    'title' => snake_case($errors['title']),
                    'detail' => $errors['detail'],
                ];
                break;
            }
        }
        return response()->json(["errors" => $errorsArray], $code);
    }
}
if (!function_exists('getValueFromAcceptCriteria')) {
    function getValueFromAcceptCriteria($acceptCriteria, $value, $withSlug = false)
    {
        $acceptCriteria = json_decode($acceptCriteria);
        $value = $acceptCriteria->{$value};
        $data['value'] = $value;
        $slug = '';
        if ($withSlug) {
            $option = Option::find($value);
            if ($option) {
                $slug = Option::find($value)->slug;;
            }
        }
        $data['slug'] = $slug;

        return $data;
    }
}
if (!function_exists('getNumberOfPercent')) {
    function getNumberOfPercent($number, $total)
    {
        return ($number / $total) * 100;
    }
}

if (!function_exists('calculateQuestionResult')) {
    /**
     * @param int $allCorrectAnswer
     * @param int $allAnswer
     * @return string|null
     */
    function calculateQuestionResult(int $allCorrectAnswer, int $allAnswer)
    {
        if ($allAnswer == 0) {
            return null;
        } else {
            $percentageResult = ($allCorrectAnswer / $allAnswer) * 100;
            return \App\Modules\LearningResources\Enums\DifficultlyLevelEnums::percentageDifficultyLevel($percentageResult);
        }
    }
}
if (!function_exists('getResourceData')) {
    function getResourceData($resourceSubjectFormatSubject)
    {
        $data = null;
        switch ($resourceSubjectFormatSubject->resource_slug) {

            case LearningResourcesEnums::TRUE_FALSE:
                $data = TrueFalseData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )
                    ->with('questions.options')->first();

                break;
            case LearningResourcesEnums::MULTI_CHOICE:
                $data = MultipleChoiceData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )
                    ->with('questions.options')->first();

                break;
            case LearningResourcesEnums::Video:
                $data = VideoData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();

                break;
            case LearningResourcesEnums::DRAG_DROP:
                $data = DragDropData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->with('questions')->with('options')->first();
                break;
            case LearningResourcesEnums::MATCHING:
                $data = MatchingData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();

                break;
            case LearningResourcesEnums::MULTIPLE_MATCHING:
                $data = MultiMatchingData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();

                break;

            case LearningResourcesEnums::PAGE:

                $data = PageData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();

                break;
            case LearningResourcesEnums::Audio:
                $data = AudioData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();


                break;
            case LearningResourcesEnums::PDF:
                $data = PdfData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();

                break;
            case LearningResourcesEnums::PICTURE:
                $data = PictureData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();

                break;

            case LearningResourcesEnums::FLASH:
                $data = FlashData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();

                break;
            case LearningResourcesEnums::COMPLETE:
                $data = CompleteData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();

                break;
        }
        return $data;
    }
}

if (!function_exists('getSectionChilds')) {
    function getSectionChilds($subjectFormatSubject)
    {
        $ids = [];

        $ids[] = $subjectFormatSubject->id;

        if ($subjectFormatSubject->childSubjectFormatSubject) {
            foreach ($subjectFormatSubject->childSubjectFormatSubject as $subSubjectSection) {
                $ids[] = $subSubjectSection->id;

                foreach ($subSubjectSection->childSubjectFormatSubject as $subSubSection) {
                    $ids[] = $subSubSection->id;

                    foreach ($subSubSection->childSubjectFormatSubject as $subSubSubSection) {
                        $ids[] = $subSubSubSection->id;
                    }
                }
            }
        }

        return $ids;
    }
}

if (!function_exists('getSectionsOfSections')) {
    function getSectionsOfSections(array $sectionIds)
    {
        $sectionsArray = [];

        foreach ($sectionIds as $id) {
            # code...
            $section = SubjectFormatSubject::with('childSubjectFormatSubject.childSubjectFormatSubject.childSubjectFormatSubject')->findOrFail($id);

            $sectionsArray[] = getSectionChilds($section);
        }

        return array_flatten($sectionsArray);
    }
}

if (!function_exists('markSectionProgress')) {
    function markSectionProgress(array $sectionIds, $subjectId, $student)
    {
        //mark sections as visible
        SubjectFormatProgressStudent::where('is_visible', false)->whereIn('subject_format_id', $sectionIds)->where('student_id', $student->id)->update(['is_visible' => true]);
        ResourceProgressStudent::where('is_visible', false)->whereIn('subject_format_id', $sectionIds)->where('student_id', $student->id)->update(['is_visible' => true]);

        $subject = Subject::find($subjectId);

        //calculate progress
        $points = ResourceProgressStudent::where('is_visible', true)
            ->where('student_id', $student->id)
            ->where('subject_id', $subjectId)
            ->sum('points');

        $subjectProgressPercentage = 0;
        if ($subject->total_points > 0) {
            $subjectProgressPercentage = $points / $subject->total_points * 100;
        }
        $student->subscribe()->where('subject_id', $subjectId)->update([
            'subject_progress' => $points,
            'subject_progress_percentage' => $subjectProgressPercentage
        ]);
    }
}
if (!function_exists('is_student_subscribed')) {
    function is_student_subscribed($subject, $child = null)
    {
        $authUser = auth()->user();

        if ($child && $child->student) {
            $student = $child->student;
        } else {
            $student = $authUser->student;
        }
        if (!$student) {
            return false;
        }
        $userIsSubscriped = DB::table('subject_subscribe_students')
            ->where('subject_id', $subject->id)
            ->where('student_id', $student->id)
            ->exists();

        return $userIsSubscriped;
    }
}

if (!function_exists('agoraFinishSession')){
    function agoraFinishSession($vcrSession)
    {
        $user = $vcrSession->instructor;
        $userUUID = $vcrSession->agora_instructor_uuid;
        $paramData = [
            "userName"=> $user->first_name .' '.$user->last_name,
            "role"=> "host",
            "streamUuid"=> "0",
        ];
        $response = AgoraHandlerV2::makeRequest('/rooms/'.$vcrSession->room_uuid.'/users/'.$userUUID.'/entry/', 'post', $paramData);
        if (isset($response['data'])){
            $userToken = $response['data']['user']['userToken'];
            AgoraHandlerV2::makeRequest(
                'rooms/' . $vcrSession->room_uuid.'/states/2',
                'put',
                [],
                $userToken
            );
        }
    }
}

if (!function_exists('calculateSubjectProgress')) {

    function calculateSubjectProgress($subject, $child = null)
    {
        $authUser = auth()->user();

        if ($child && $child->student) {
            $student = $child->student;
        } else {
            $student = $authUser->student;
        }

//      return  ($subject->total_points > 0) ?
//          (($subject->studentProgress->where('student_id' , $student->id)->first()->points ?? 0) / ($subject->total_points * 100))
//          : 0;

        return ($subject->total_points > 0) ? (($student->subscribe()->where('subject_id', $subject->id)->first()->subject_progress ?? 0) / ($subject->total_points)) * 100 : 0;
    }
}

if(!function_exists('calculateCourseProgress')){
    function calculateCourseProgress($course,$child = null){
        $authUser = auth()->user();

        if ($child && $child->student) {
            $student = $child->student->user;
        } else {
            $student = $authUser;
        }
        $sessions_count = $course->sessions->count();
        $attended_sessions = $student->VCRSessionsPresence()->whereHas('vcrSession',function ($query) use($course){
            $query->where('course_id',$course->id);
        })->count();
        return ($sessions_count > 0) ? ($attended_sessions /$sessions_count ) * 100 : 0;
    }
}

if (!function_exists('calculateSectionProgress')) {
    function calculateSectionProgress($section, $child = null)
    {
        $authUser = auth()->user();

        if ($child && $child->student) {
            $student = $child->student;
        } else {
            $student = $authUser->student;
        }
        $currentProgress = SubjectFormatProgressStudent::where('student_id', $student->id)
            ->where('subject_format_id', $section->id)
            ->where('is_visible', 1)
            ->first();
        $currentProgress = $currentProgress ? $currentProgress->points : 0;
        return ($section->total_points > 0) ? ($currentProgress / $section->total_points) * 100 : 0;
    }
}
if (!function_exists('calculateResourceProgress')) {
    function calculateResourceProgress($resource, $child = null)
    {
        $authUser = auth()->user();

        if ($child && $child->student) {
            $student = $child->student;
        } else {
            $student = $authUser->student;
        }

        $currentProgress = ResourceProgressStudent::where('student_id', $student->id)
            ->where('resource_id', $resource->id)
            ->where('is_visible', 1)
            ->first();
        $currentProgress = $currentProgress ? $currentProgress->points : 0;
        return ($resource->total_points > 0) ? ($currentProgress / $resource->total_points) * 100 : 0;
    }
}

if (!function_exists('is_student_subscribed_to_course')) {

    function is_student_subscribed_to_course($course, $child = null)
    {
        $authUser = auth()->user();

        if ($child && $child->student) {
            $student = $child->student;
        } else {
            $student = $authUser->student;
        }

        $userIsSubscribed = DB::table('course_student')
            ->where('course_id', $course->id)
            ->where('student_id', $student->id)
            ->exists();

        return $userIsSubscribed;
    }
}
if (!function_exists('is_student_subscribed_to_package')) {

    function is_student_subscribed_to_package($package, $child = null)
    {
        $authUser = auth()->user();

        if ($child && $child->student) {
            $student = $child->student;
        } else {
            $student = $authUser->student;
        }

        $userIsSubscribed = DB::table('packages_subscribed_students')
            ->where('package_id', $package->id)
            ->where('student_id', $student->id)
            ->exists();

        return $userIsSubscribed;
    }
}

if (!function_exists('get_ratings')) {

    function get_ratings($ratedClass, $ratedId)
    {
        return $ratings = DB::table('ratings')
            ->where('ratingable_type', $ratedClass)
            ->where('ratingable_id', $ratedId)
            ->get();
    }
}

if (!function_exists('get_percentage')) {

    function get_percentage($element, $total)
    {
        $percent = $element / $total;
        return $percent * 100;
    }
}

if (!function_exists('attachQuestionMeida')) {


    function attachQuestionMeida($question, $object, $path)
    {
        $questionMediaEnabled = env('QUESTION_MEDIA', true);

        if (!method_exists($object, 'media') || !$questionMediaEnabled) {
            return;
        }

        $relation = $object->media();
        if (isset($question->detach_media)) {
            deleteMedia($question->detach_media, $relation, $path);
        }
        if (isset($question->attach_media)) {
            //To Remove Old & duplication
            $oldIds = $relation->pluck('id')->toArray();
            if (count($oldIds) > 0) {
                deleteMedia($oldIds, $relation, $path);
            }

            moveGarbageMedia($question->attach_media, $relation, $path);

        }


    }
}
if (!function_exists('attachQuestionVideo')) {


    function attachQuestionVideo($question, $object, $path)
    {
        $questionMediaEnabled = env('QUESTION_MEDIA', true);

        if (!method_exists($object, 'video') || !$questionMediaEnabled) {
            return;
        }
        $relation = $object->video();
        if (isset($question->detach_video)) {
            deleteMedia($question->detach_video, $relation, $path);
        }
        if (isset($question->attach_video)) {
            //To Remove Old & duplication
            $oldIds = $relation->pluck('id')->toArray();
            if (count($oldIds) > 0) {
                deleteMedia($oldIds, $relation, $path);
            }

            moveGarbageMedia($question->attach_video, $relation, $path);

        }


    }
}
if (!function_exists('attachQuestionAudio')) {


    function attachQuestionAudio($question, $object, $path)
    {
        $questionMediaEnabled = env('QUESTION_MEDIA', true);

        if (!method_exists($object, 'audio') || !$questionMediaEnabled) {
            return;
        }

        $relation = $object->audio();
        if (isset($question->detach_audio)) {
            deleteMedia($question->detach_audio, $relation, $path);
        }
        if (isset($question->attach_audio)) {
            //To Remove Old & duplication
            $oldIds = $relation->pluck('id')->toArray();
            if (count($oldIds) > 0) {
                deleteMedia($oldIds, $relation, $path);
            }

            moveGarbageMedia($question->attach_audio, $relation, $path);

        }


    }
}

if (!function_exists('questionMedia')) {
    function questionMedia($object)
    {
        $media = $object->media;
        if ($media) {
            $questionMediaEnabled = env('QUESTION_MEDIA', true);
            if (!empty($media->filename)) {
                $url = resourceMediaUrl($media->filename);
            } else {
                $url = resourceMediaUrl($media->source_filename);
            }
            if ($questionMediaEnabled && $url) {
                return [
                    'id' => (int)$media->id,
                    'url' => $url,
                    'filename' => $media->filename,
                    'width' => ResourcesConfigurationEnum::QUESTION_IMAGE_WIDTH,
                    'height' => ResourcesConfigurationEnum::QUESTION_IMAGE_HEIGHT,
                    'source_filename' => $media->source_filename,
                ];
            }
        }
        return [];
    }
}
function questionAudio($object)
{
    $media = $object->audio;
    if ($media) {
        $questionMediaEnabled = env('QUESTION_MEDIA', true);
        if (!empty($media->filename)) {
            $url = resourceMediaUrl($media->filename);
        } else {
            $url = resourceMediaUrl($media->source_filename);

        }
        if ($questionMediaEnabled && $url) {
            return [
                'id' => (int) $media->id,
                'url' => $url,
                'filename' => $media->filename,

                'source_filename' => $media->source_filename,
            ];
        }
    }
    return [];

}
if (!function_exists('getMaxUploadSize')) {
    function questionVideo($object)
    {
        $media = $object->video;
//    dd($media);
        if ($media) {
            $questionMediaEnabled = env('QUESTION_MEDIA', true);
            if (!empty($media->filename)) {
                $url = resourceMediaUrl($media->filename);
            } else {
                $url = resourceMediaUrl($media->source_filename);
            }
            if ($questionMediaEnabled && $url) {
                return [
                    'id' => (int)$media->id,
                    'url' => $url,
                    'filename' => $media->filename,

                    'source_filename' => $media->source_filename,
                ];
            }
        }
        return [];
    }
}
if (!function_exists('getMaxUploadSize')) {
    function getMaxUploadSize()
    {
        return min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
    }
}
if (!function_exists('getMediaValidationRule')) {
    function getMediaValidationRule()
    {
        return trans('app.extentions') . " (" . implode(",", getImageTypes()) . ") , " . trans("app.dimentions") . ": (1400*900) ," . trans("app.max size") . ":" . getMaxUploadSize();
    }
}

if (!function_exists('getResourceRules')) {
    function getResourceRules($slug)
    {
        switch ($slug) {
            case LearningResourcesEnums::TRUE_FALSE:
            case LearningResourcesEnums::MULTI_CHOICE:
            case LearningResourcesEnums::DRAG_DROP:
            case LearningResourcesEnums::MATCHING:
            case LearningResourcesEnums::MULTIPLE_MATCHING:
            case LearningResourcesEnums::HOTSPOT:
            case LearningResourcesEnums::COMPLETE:
            case LearningResourcesEnums::PICTURE:
                return getMediaValidationRule();
            case LearningResourcesEnums::PAGE:

                return "";
                break;
            case LearningResourcesEnums::Audio:

                return trans('app.extentions') . implode(",", MediaEnums::AUDIO_TYPES) . " , " . trans("app.max size") . ":" . getMaxUploadSize();
                break;
            case LearningResourcesEnums::PDF:

                return trans('app.extentions') . implode(",", MediaEnums::PDF_TYPES) . ", " . trans("app.max size") . ":" . getMaxUploadSize();
                break;

            case LearningResourcesEnums::FLASH:

                return trans('app.extentions') . implode(",", MediaEnums::FLASH_TYPES) . ", " . trans("app.max size") . ":" . getMaxUploadSize();
                break;
            case LearningResourcesEnums::Video:

                return trans('app.extentions') . implode(",", MediaEnums::VIDEO_TYPES) . ", " . trans("app.max size") . ":" . getMaxUploadSize();
                break;

            case "subject_media":

                return trans('app.extentions') . implode(",", MediaEnums::SUPPORTED_TYPES) . ", " . trans("app.dimentions") . ": (1400*900) ," . trans("app.max size") . ":" . getMaxUploadSize();
                break;

        }

    }
}


if (!function_exists('getSectionParent')) {

    function getSectionParent($sectionId)
    {
        $section = SubjectFormatSubject::findOrFail($sectionId);

        if ($section->parent_subject_format_id) {
            return $section->parentSubjectFormatSubject;
        } else {
            return null;
        }
    }
}

if (!function_exists('getTitleFromSections')) {
    function getTitleFromSections($subjectFormatSubjects)
    {
        if ($subjectFormatSubjects->where('parent_subject_format_id', null)->count() >= 2) {

            return $subjectFormatSubjects->first()->subject->name;
        }
        $examTitle = '';
        foreach ($subjectFormatSubjects as $subjectFormatSubject) {
            if ($subjectFormatSubject != $subjectFormatSubjects->last()) {
                $examTitle .= $subjectFormatSubject->title . ' - ';
            } else {
                $examTitle .= $subjectFormatSubject->title;
            }
        }
        // any section in the sections collection will be enough
        // because all of them in the same level
        $examTitle = getTitleFromParentSections($examTitle, $subjectFormatSubjects->first());
        return $examTitle;
    }
}

if (!function_exists('getTitleFromParentSections')) {
    function getTitleFromParentSections($examTitle, $parentSection)
    {
        while ($parentSection->parentSubjectFormatSubject()->exists()) {
            $examTitle .= ' - ' . $parentSection->parentSubjectFormatSubject->title;
            $parentSection = $parentSection->parentSubjectFormatSubject;
        }
        $examTitle .= ' - ' . $parentSection->subject->name;
        return $examTitle;
    }
}

if (!function_exists('deleteQuestionMedia')) {
    function deleteQuestionMedia($media)
    {
        if (in_array($media->extension, getImageTypes())) {
            deleteImagePath(S3Enums::LARGE_PATH . $media->filename);
            deleteImagePath(S3Enums::SMALL_PATH. $media->filename);
        } else {
            deleteImagePath(S3Enums::LARGE_PATH . $media->filename);
        }
        $media->delete();
    }
}

if (!function_exists('allowedQuestionsCountForExam')) {
    function allowedQuestionsCountForExam()
    {
        $allowedArr = [];
        for ($i = 1; $i <= 12; $i++) {
            array_push($allowedArr, 5 * $i);
        }
        return $allowedArr;
    }
}

if (!function_exists('dummyPicture')) {
    function dummyPicture()
    {
        return asset('img/avatar.png');
    }
}


if (!function_exists('examTitle')) {

    function examTitle($examType, $examTitle)
    {
        if ($examType == ExamTypes::EXAM) {
            return $examTitle;
        }
        if ($examType == ExamTypes::PRACTICE) {
            return $examTitle;
        }
        if ($examType == ExamTypes::EXAM || $examType == ExamTypes::INSTRUCTOR_COMPETITION) {
            return $examTitle;
        }

         return $examTitle;
    }
}

if (!function_exists('randomEquation')) {

    function randomEquation()
    {
        $arrayOfEquations = [
            '<math xmlns="http://www.w3.org/1998/Math/MathML"><mfrac><mn>1</mn><mn>2</mn></mfrac><msqrt><mn>25</mn></msqrt></math>',
            '<math xmlns="http://www.w3.org/1998/Math/MathML"><mfenced open="|" close="|"><mrow><mo>-</mo><msqrt><mn>4</mn></msqrt></mrow></mfenced></math>',
            '<math xmlns="http://www.w3.org/1998/Math/MathML"><mfenced><mtable><mtr><mtd><mn>2</mn></mtd><mtd><mn>3</mn></mtd><mtd><mn>5</mn></mtd></mtr><mtr><mtd><mn>8</mn></mtd><mtd><mn>5</mn></mtd><mtd><mn>0</mn></mtd></mtr><mtr><mtd><mn>7</mn></mtd><mtd><mn>0</mn></mtd><mtd><mn>1</mn></mtd></mtr></mtable></mfenced><mo>&#xB7;</mo><mfenced open="[" close="]"><mtable><mtr><mtd><mn>6</mn></mtd></mtr><mtr><mtd><mo>-</mo><mn>5</mn></mtd></mtr><mtr><mtd><mn>9</mn></mtd></mtr></mtable></mfenced></math>'
        ];
        return $arrayOfEquations[array_rand($arrayOfEquations)];
    }
}

if (!function_exists('bringDBNotificationData')) {

    function bringDBNotificationData($title, $body, $screenType, $url)
    {

        foreach (config('translatable.locales') as $locale) {
            $multiLanguageData[$locale] =
                [
                    'title' => trans($title, [], $locale),
                    'body' => trans($body, [], $locale),
                ];
        }

        return [
            'data' => [
                'title' => trans($title),
                'body' => trans($body),
                'screen_type' => $screenType,
                'url' => $url,
                'localization' => $multiLanguageData,
            ]
        ];
    }
}


// given section, sorts parent section ids from subject down to it starting with its subject id (for breadcrumbs)
if (!function_exists('getBreadcrumbsIds')) {

    function getBreadcrumbsIds($subjectFormatSubject, $arr)
    {
        $arr[] = $subjectFormatSubject->id;
        if (isset($subjectFormatSubject->parentSubjectFormatSubject))
            return getBreadcrumbsIds($subjectFormatSubject->parentSubjectFormatSubject, $arr);

        //add subject id
        $arr[] = $subjectFormatSubject->subject->id;
        $arr = array_reverse($arr);

        $result = [];
        foreach ($arr as $key => $value) {
            $data = [];
            $data['index'] = $key;
            $data['id'] = $value;
            $result[] = $data;
        }

        return $result;
    }
}
// given section, sorts parent section ids from subject down to it starting with its subject id (for breadcrumbs)
if (!function_exists('getDynamicLink')) {
    function getDynamicLink($link, $params = [])
    {

        foreach ($params as $key => $value) {
            $link = str_replace('{' . $key . '}', $value, $link);
        }
        return ($link);

    }
}

if (!function_exists('buildTranslationKey')) {
    function buildTranslationKey(string $trans_key, array $trans_params = [])
    {
        if (count($trans_params)) {
            $translation = [
                'trans_key' => $trans_key,
                'trans_params' => $trans_params
            ];
        } else {
            $translation = $trans_key;
        }
        return ($translation);
    }
}
if (!function_exists('displayTranslation')) {
    function displayTranslation($key, $lang = null)
    {
        if (is_array($key)) {
            $translation = trans($key['trans_key'], $key['trans_params'], $lang);
        } else {
            $translation = trans($key, [], $lang);
        }
        return ($translation);
    }
}
if (!function_exists('getSessionUrls')) {
    function getStudentSessionUrls($sessionId, $type, $token = null, $user = null)
    {
        if (is_null($user)){
            $user = auth()->user();
        }
        if(is_null($token)){
            $tokenManager = app(TokenManagerInterface::class);
            $token = $tokenManager->createUserToken(TokenNameEnum::DYNAMIC_lINKS_Token,$user);
        }

        $apiUrl = buildScopeRoute('api.online-sessions.getVCRSession', ['sessionId' => $sessionId, 'type' => $type]);
        $webUrl = getDynamicLink(DynamicLinksEnum::INSTRUCTOR_JOIN_ROOM,
                        ['session_id' => $sessionId,
                            'token' => $token,
                            'type' => $type,
                            'portal_url' => env('VCR_PORTAL_URL','https://vcr.ta3lom.com')
                        ]);
        return [
            'api_url' => $apiUrl,
            'web_url' => $webUrl,
        ];
    }

}

if (!function_exists('getStudentSubjectTimeInHours')) {
    function getStudentSubjectTimeInHours($subject, $student)
    {
        return number_format(SubjectTime::where('subject_id', $subject->id)
                ->where('student_id', $student->id)
                ->sum('time') / (60 * 60), 2);
    }
}


if (!function_exists('getVCRSessionFromCourseSessionByParticipant')) {
    function getVCRSessionFromCourseSessionByParticipant($courseSession, $participant)
    {
        return $courseSession->VCRSession()
            ->whereHas('participants', function ($q) use ($participant) {
                $q->where('user_id', $participant->user_id);
            })->first();
    }
}

if (!function_exists('getParticipantFromVCRSession')) {
    function getParticipantFromVCRSession($vcrSession, $participant)
    {
        return $vcrSession->participants()
            ->where('user_id', $participant->user_id)
            ->first();
    }
}

if (!function_exists('getSchoolLogo')) {
    function getSchoolLogo()
    {
        $logo = '';
        $authUser = auth()->user();
        if ($authUser) {
            if ($authUser->type == UserEnums::SCHOOL_ACCOUNT_MANAGER) {
                $logo = $authUser->schoolAccount->logo ?? "";
            } elseif($authUser->type == UserEnums::SCHOOL_ADMIN){
                $logo = $authUser->schoolAdmin->currentSchool->logo ?? "";
            } elseif (in_array($authUser->type, [UserEnums::SCHOOL_SUPERVISOR, UserEnums::SCHOOL_LEADER])) {
                $logo = $authUser->schoolAccountBranchType->schoolAccount->logo ?? "";
            }  elseif($authUser->type == UserEnums::EDUCATIONAL_SUPERVISOR){
                $logo = $authUser->branches()->first()->schoolAccount->logo ?? "";
            } elseif ($authUser->type == UserEnums::STUDENT_TYPE) {
                //@todo add school id in student
                if ($authUser->student->classroom) {
                    $logo = $authUser->student->classroom->branch->schoolAccount->logo ?? "";

                }
            }
        }

        return imageProfileApi($logo);
    }
}
if (!function_exists('truncateString')) {

    function truncateString($text, $length)
    {
        $length = abs((int)$length);
        if (strlen($text) > $length) {
            $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $text);
        }
        return ($text);
    }

}
if (!function_exists('getEnglishOrdinalsuffix')) {

    function getEnglishOrdinalsuffix($value)
    {
        $number = abs($value);

        if (class_exists('NumberFormatter')) {
            $nf = new \NumberFormatter('en_US', \NumberFormatter::ORDINAL);

            return $nf->format($number);
        }


        $indicators = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];

        $suffix =  $indicators[$number % 10];
        if ($number % 100 >= 11 && $number % 100 <= 13) {
            $suffix = 'th';
        }

        return number_format($number) . $suffix;

    }
}

if (!function_exists('getArabicOrdinalsuffix')) {

    function getArabicOrdinalsuffix($number)
    {
        $Arabic = new I18N_Arabic_Numbers();
        if($number == 1){

        return trans('exam.rank') . ' ' . $Arabic->setFeminine(2)->setOrder(2)->int2str($number);
      }
        if($number > 1 && $number < 11){

        return trans('exam.rank') .  ' ' . 'ال' . $Arabic->setFeminine(2)->setOrder(2)->int2str($number);
      }

      return trans('exam.rank') . ' ' . $Arabic->setFeminine(2)->setOrder(3)->int2str( $number);
    }
    }

    if (! function_exists('str_ordinal')) {
        /**
         * Append an ordinal indicator to a numeric value.
         *
         * @param  string|int  $value
         * @param  bool  $superscript
         * @return string
         */
        function str_ordinal($value, $superscript = false)
        {
            $number = abs($value);

            $indicators = ['th','first','second','third ','four','five','six','seven','th','th'];

            $suffix = $superscript ? '<sup>' . $indicators[$number % 10] . '</sup>' : $indicators[$number % 10];

            if ($number % 100 >= 11 && $number % 100 <= 13) {
                $suffix = $superscript ? '<sup>th</sup>' : 'th';
            }

            return  $suffix . ' ' . trans('exam.rank');
        }
    }

    if (! function_exists('getOrdinal')) {
        function getOrdinal($number){

            if(app()->getLocale() == 'ar'){
                return getArabicOrdinalsuffix($number);
            }

            return str_ordinal($number);
        }
    }


    if (!function_exists('endOfDay')) {
        function endOfDay($day)
        {
            return Carbon::createFromFormat('Y-m-d', $day)
                ->endOfDay()
                ->toDateTimeString();
        }
    }

    if (!function_exists('startOfDay')) {
        function startOfDay($day)
        {
            return Carbon::createFromFormat('Y-m-d', $day)
                ->startOfDay()
                ->toDateTimeString();
        }
    }


if (!function_exists('dayRepeated')) {

    function dayRepeated($day, $startDate, $endDate)
    {
        $repeated = [];
        $endDate = strtotime($endDate);
        for ($i = strtotime($day, strtotime($startDate)); $i <= $endDate; $i = strtotime('+1 week', $i))
            array_push($repeated, date('l Y-m-d', $i));

        return $repeated;
    }
}

if (!function_exists('calculateAttendanceSessions')) {
    function calculateAttendanceSessions($course): int
    {
        $user = auth()->user();
        $attendedSessions = 0;
        if (isset($user) && $user->type == UserEnums::STUDENT_TYPE) {
            $attendedSessions = $user->VCRSessionsPresence()
                ->whereHas(
                    'vcrSession',
                    function ($query) use ($course) {
                        $query->where('course_id', $course->id);
                    }
                )
                ->count();
        }

        return $attendedSessions;
    }
}
if (!function_exists('dayRepeated')) {

    function dayRepeated($day, $startDate, $endDate)
    {
        $repeated = [];
        $endDate = strtotime($endDate);
        for ($i = strtotime($day, strtotime($startDate)); $i <= $endDate; $i = strtotime('+1 week', $i))
            array_push($repeated, date('Y-m-d', $i));

        return $repeated;
    }
}

if (!function_exists('total_correct_answer')) {

    function total_correct_answer($exam, $student)
    {
        $totalCorrectAnswers = CompetitionQuestionStudent::where('student_id' , $student->id)
        ->where('exam_id' , $exam->id)
        ->sum('is_correct_answer');

    return $totalCorrectAnswers;

    }


}

if (!function_exists('resolveSubscribableName')) {

    function resolveSubscribableName($transaction)
    {
        $subscribable = $transaction->detail->subscribable;
        $subscribableType = $transaction->detail->subscribable_type;
        $type = "";
        $name = "";

        switch ($subscribableType) {
            case Subject::class:
                $type = trans('app.Subject');
                $name = $subscribable?->name;
                break;
            case Course::class:
                $type = $subscribable->type == CourseEnums::LIVE_SESSION ? trans('app.Live Session') :  trans('app.course');
                $name = $subscribable?->name;
                break;
            case Package::class:
                $type = trans('app.SubjectPackage');
                $name = $subscribable?->name;
                break;
            case VCRRequest::class:
                $type = trans('app.vcr_request');
                $name = $subscribable ? $subscribable->instructor?->name : "";
                break;
        }

        return ['type' => $type, 'name' => $name];
    }

    if (!function_exists('getImagePath')) {
        function getImagePath($imagePath, $alt = null)
        {
            if (Storage::exists($imagePath)) {
                return Storage::url($imagePath);
            }
            if ($alt) {
                return $alt;
            }
            return "https://via.placeholder.com/500";
        }
    }
    if (!function_exists('deleteImagePath')) {
        function deleteImagePath($imagePath):void
        {
            if (Storage::exists($imagePath)) {
                Storage::delete($imagePath);
            }
        }
    }
    if (!function_exists('moveImagePath')) {
        function moveImagePath($pathFrom , $pathTo,$storagePath,$fileName)
        {
            if (!in_array($pathTo.$storagePath , Storage::directories())) {
                Storage::makeDirectory($pathTo.$storagePath);
            }
            Storage::move($pathFrom . $fileName ,$pathTo . $storagePath . '/' . $fileName);
        }
    }
    if (!function_exists('getImageSize')) {
        function getImageSize($imagePath)
        {
            if (Storage::exists($imagePath)) {
               return Storage::size($imagePath);
            }
            return 0;
        }
    }
    if (!function_exists('reSizeImage')) {
        function reSizeImage($pathFrom, $width, $height, $imageExt, $pathTo, $resizeType)
        {
            if (Storage::exists($pathFrom)) {
                $image = Intervention\Image\Facades\Image::make(getImagePath($pathFrom));
                if ($resizeType == S3Enums::RESIZE) {
                    $image->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                } elseif ($resizeType == S3Enums::CROP) {
                    $image->fit($width, $height);
                }

                $image->encode($imageExt, 60);
                Storage::put($pathTo, $image->__toString());
            }
        }
    }
}

