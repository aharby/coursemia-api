<?php


namespace App\OurEdu\Certificates\Admin\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\Certificates\Admin\Requests\ThankingCertificatesRequest;
use App\OurEdu\Certificates\Models\ThankingCertificate;
use App\OurEdu\Certificates\Repositories\ThankingCertificatesRepositoryInterface;
use App\OurEdu\Certificates\UseCases\ThankingCertificatesUseCaseInterface;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ThankingCertificateController extends BaseController
{
    /**
     * @var ThankingCertificatesRepositoryInterface
     */
    private $certificatesRepository;
    /**
     * @var ThankingCertificatesUseCaseInterface
     */
    private $certificatesUseCase;

    /**
     * ThankingCertificateController constructor.
     * @param ThankingCertificatesRepositoryInterface $certificatesRepository
     * @param ThankingCertificatesUseCaseInterface $certificatesUseCase
     */
    public function __construct(ThankingCertificatesRepositoryInterface $certificatesRepository, ThankingCertificatesUseCaseInterface $certificatesUseCase)
    {
        $this->certificatesRepository = $certificatesRepository;
        $this->certificatesUseCase = $certificatesUseCase;
    }

    public function index()
    {
        $data['rows'] = ThankingCertificate::query()->paginate(env('PAGE_LIMIT', 20));

        return view("admin.certificates.thanking.index", $data);
    }

    public function create()
    {
        return view("admin.certificates.thanking.create");
    }

    public function store(ThankingCertificatesRequest $request)
    {

        $data = $request->except(["attributes"]);
        $data['attributes'] = json_encode($request->get("attributes"));
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = $file->getClientOriginalName();
            $filePath = "thanking-certificates" . '/' . $name;

            if (Storage::disk('s3')->put($filePath, file_get_contents($file))) {

                $certificate = ThankingCertificate::query()->create([
                    "image" => Storage::disk('s3')->url($filePath),
                    "attributes" => $data["attributes"]
                ]);

                flash()->success(trans('app.Created successfully'));
            }

            else {
                flash()->error(trans('app.Created successfully'));
            }
        }

        return redirect()->route("admin.certificates.thanking.index");
    }

    public function edit(ThankingCertificate $certificate) {
        $date["row"] = $certificate;

        return view("admin.certificates.thanking.edit", $date);
    }

    public function update(ThankingCertificatesRequest $request, ThankingCertificate $certificate)
    {

        $data = $request->except(["attributes"]);
        $data['attributes'] = json_encode($request->get("attributes"));

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = $file->getClientOriginalName();
            $filePath = "thanking-certificates" . '/' . $name;

            if (Storage::disk('s3')->put($filePath, file_get_contents($file))) {
                $data["image"] = Storage::disk('s3')->url($filePath);
            }
        }

        $certificate = $this->certificatesRepository->update($certificate, $data);

        return redirect()->route("admin.certificates.thanking.index");
    }

    public function PrintDemoCertificate (ThankingCertificate $certificate)
    {
        $student = User::query()->where("type", "=", UserEnums::STUDENT_TYPE)->inRandomOrder()->first();
        $teacher = User::query()->where("type", "=", UserEnums::INSTRUCTOR_TYPE)->inRandomOrder()->first();
        $subject = Subject::query()->inRandomOrder()->first();

        $data = [
            "name" => trim($student->name),
            "teacher" => $teacher->name,
            "subject" => Carbon::now()->format("Y/m/d"),
        ];

        $this->certificatesUseCase->printCertificate($certificate, $data);
    }

    private function saveDemoCertificate (ThankingCertificate $certificate)
    {
        $student = User::query()->where("type", "=", UserEnums::STUDENT_TYPE)->inRandomOrder()->first();
        $teacher = User::query()->where("type", "=", UserEnums::INSTRUCTOR_TYPE)->inRandomOrder()->first();
        $subject = Subject::query()->inRandomOrder()->first();

        $data = [
            "name" => trim($student->name),
            "teacher" => $teacher->name,
            "subject" => Carbon::now()->format("Y/m/d")
        ];

        $image = $this->certificatesUseCase->saveCertificate($certificate, 'demo', $data);
        $certificate->demo = $image;
        $certificate->save();

    }

    public function destroy(ThankingCertificate $certificate)
    {
        $certificate->delete();

        flash()->success(trans('app.Created successfully'));
        return redirect()->back();

    }
}
