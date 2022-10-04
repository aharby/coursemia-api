<?php


namespace App\OurEdu\Certificates\UseCases;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\Certificates\Models\ThankingCertificate;
use App\OurEdu\ExternalServices\ArabicHandling\Arabic\I18N\I18N_Arabic;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ThankingCertificatesUseCase implements ThankingCertificatesUseCaseInterface
{
    /**
     * @var I18N_Arabic
     */
    private $Arabic;
    /**
     * @var string
     */
    private $fontFile;

    /**
     * ThankingCertificatesUseCase constructor.
     * @param string $fontFilePath
     */
    public function __construct(string $fontFilePath = 'fonts/ae_AlHor.ttf')
    {
        $this->Arabic = new I18N_Arabic('Glyphs');
        $this->fontFile = $fontFilePath;
    }

    /**
     * @param ThankingCertificate $certificate
     * @param array $data
     */
    public function printCertificate(ThankingCertificate $certificate, array $data)
    {
        $data["name"] = $this->removeSpaces($data["name"]);
        $data["teacher"] = $this->removeSpaces($data["teacher"]);

        $image = $this->manipulateImage($certificate, $data);
        $extension = $this->getExtension($certificate->image);

        header("Content-type: image/{$extension}");
        $this->image($image, $certificate->image);
        imagedestroy($image);
    }

    /**
     * @param ThankingCertificate $certificate
     * @param array $data
     * @param string|null $path
     */
    public function saveCertificate(ThankingCertificate $certificate, $type, array $data, string $path = null)
    {
        $extension = $this->getExtension($certificate->image);
        $data["name"] = $this->removeSpaces($data["name"]);
        $data["teacher"] = $this->removeSpaces($data["teacher"]);

        if (!$path) {
            $path = "uploads/certificates/thanking-certificates/demos";
        }

        $filename = "certificate_{$certificate->id}.{$extension}";

        if ($type =='student') {
            $prepareStendentName = Str::slug(trim($data['name']), '_');
            $filename = "certificate_{$certificate->id}_{$prepareStendentName}.{$extension}";
        }

        $temp = storage_path('app/public/uploads');
        $filename_temp = $temp."/".$filename;

        $filename = "thanking-certificates" . '/' . $filename;

        $image = $this->manipulateImage($certificate, $data);
        $this->image($image, $certificate->image, $filename_temp);
        imagedestroy($image);

        if (Storage::disk('s3')->put($filename, File::get($filename_temp))) {
            return Storage::disk("s3")->url($filename);
        }

        return null;
    }

    /**
     * @param ThankingCertificate $certificate
     * @param array $data
     * @return false|resource
     */
    private function manipulateImage(ThankingCertificate $certificate, array $data)
    {
        $attributes = $certificate->attributes;
        $image = $this->createImage($certificate->image);

        imagealphablending($image, true);
        $red = imagecolorallocate($image, 150, 0, 0);

        foreach ($attributes as $key => $coordinates) {
            $fontzixe = $coordinates['font_size'] ?? 20;
            $x = $this->textCoordinates($data[$key], $fontzixe, ($coordinates['x'] - $coordinates['x2']));

            imagefttext($image, $fontzixe, 0, ($x/2)+$coordinates['x2'], $coordinates['y'], $red, $this->fontFile, $this->Arabic->utf8Glyphs($data[$key]));
        }

        return $image;
    }

    private function createImage($path = "")
    {
        $extension = $this->getExtension($path);

        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                return imagecreatefromjpeg($path);
            case 'png':
                return imagecreatefrompng($path);
            default:
                throw new OurEduErrorException("The Certificate extension not in the supported extensions (png, jpeg, jpg");
        }
    }

    private function image($image, string $path, $filename_temp = null)
    {
        $extension = $this->getExtension($path);

        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                return imagejpeg($image, $filename_temp);
            case 'png':
                return imagepng($image, $filename_temp);
            default:
                throw new OurEduErrorException("The Certificate extension not in the supported extensions (png, jpeg, jpg");
        }
    }

    public function getExtension(string $imageName)
    {
        $splitPath = explode(".", $imageName);
        $extension = end($splitPath);

        return $extension;
    }

    private function textCoordinates($string, $font, $availableArea)
    {
        $stringLength = strlen($string);
        $newX = ($availableArea - imagettfbbox($font, 0, $this->fontFile, $string)[2]);

        if (preg_match('/[اأإء-ي]/ui', $string)) {
            $newX = ($availableArea - 15*$stringLength/1.5);
        }

        return $newX;
    }

    private function removeSpaces($string)
    {
        $names = explode(" ", $string);
        $newStrings = "";

        foreach ($names as $name) {
            if (strlen($name)) {
                $newStrings .= " " .$name;
            }
        }

        return Str::words(trim($newStrings), 3, null);
    }
}
