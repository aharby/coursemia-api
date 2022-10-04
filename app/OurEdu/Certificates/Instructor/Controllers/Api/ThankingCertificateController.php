<?php


namespace App\OurEdu\Certificates\Instructor\Controllers\Api;


use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Certificates\Models\ThankingCertificate;
use App\OurEdu\Certificates\Repositories\ThankingCertificatesRepositoryInterface;
use App\OurEdu\Certificates\Transformers\StudentThankingCertificate;
use App\OurEdu\Users\User;
use App\OurEdu\Certificates\Transformers\GetThankingCertificateTransformer;
use App\OurEdu\Certificates\Instructor\Requests\CertificateStudentRequest;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\Certificates\UseCases\ThankingCertificatesUseCaseInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ThankingCertificateController extends BaseApiController
{
    /**
     * @var ParserInterface
     */
    private $parserInterface;

    /**
     * @var ThankingCertificatesRepositoryInterface
     */
    private $certificatesRepository;

    /**
     * @var StudentThankingCertificatesUseCase
     */
    private $certificatesUseCase;

    /**
     * ThankingCertificateController constructor.
     * @param ThankingCertificatesRepositoryInterface $certificatesRepository
     * *@param ParserInterface $parserInterface
     */
    public function __construct(ThankingCertificatesRepositoryInterface $certificatesRepository , ParserInterface $parserInterface , ThankingCertificatesUseCaseInterface  $certificatesUseCase)
    {
        $this->certificatesRepository = $certificatesRepository;
        $this->parserInterface = $parserInterface;
        $this->certificatesUseCase = $certificatesUseCase;
    }


    public function index()
    {
        $certificates = $this->certificatesRepository->all()->get();

        return $this->transformDataMod($certificates, new GetThankingCertificateTransformer , ResourceTypesEnums::CERTIFICATES);
    }

    public function CertificateStudent(CertificateStudentRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $student = User::query()->findOrFail($data['student_id']);


        $data['student'] = $student;
        $data['vcrSession'] = VCRSession::query()->findOrFail( $data['vcr_session_id']);
        $data['instructor'] = User::query()->findOrFail($data['vcrSession']->instructor_id);
        $data['certificate'] = ThankingCertificate::query()->findOrFail($data['certificate_id']);

        $certificateData = [
            "name" => trim($student->name),
            "teacher" => $data['instructor']->name,
            "subject" => Carbon::now()->format("Y/m/d")
        ];

        $image = $this->certificatesUseCase->saveCertificate($data['certificate'],'student',$certificateData,'uploads/certificates/thanking-certificates/students');

        $student->certificates()->attach($data['certificate_id'], ["vcr_session_id" => $data['vcr_session_id'] , 'image' => $image]);
        $certificate_image = $student->certificates()->wherepivot('vcr_session_id',$data['vcr_session_id'])->wherepivot('thanking_certificate_id',$data['certificate_id'])->first();
        if($certificate_image && $certificate_image->pivot->id){
            $data['image'] = url("/get-certificate/".$certificate_image->pivot->id);
        }

        return $this->transformDataMod($data , new StudentThankingCertificate() , ResourceTypesEnums::STUDENT_CERTIFICATE);

    }


    public function getCertificate($id){
        $certificate = \DB::table('thanking_certificate_user')->where('id',$id)->first();
        if(!$certificate){
            throw new ModelNotFoundException();
        }
        $image = $certificate->image;

        $urlDetails = parse_url($image);

        $file =  Storage::disk('s3')->get($urlDetails["path"]);

        $extension = $this->certificatesUseCase->getExtension($image);

        $headers = [
            'Content-Type' => "image/{$extension}",
            'Content-Description' => 'File Transfer'
        ];

        return response($file, 200, $headers);
    }
}
