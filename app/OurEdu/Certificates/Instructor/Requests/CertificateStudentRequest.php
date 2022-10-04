<?php


namespace App\OurEdu\Certificates\Instructor\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Users\User;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Carbon\Carbon;

class CertificateStudentRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            "attributes.student_id" =>
                ["required",
                    "exists:users,id",
                    "exists:students,user_id",
                    function ($attribute, $value, $fail) {
                        $attributes = $this->request->get('data')['attributes'];

                        $session = VCRSession::query()->findOrFail($attributes['vcr_session_id']);

                        if ($session && !$session->classroom->students()->where('user_id', $attributes['student_id'])->first()) {
                            $fail($attribute . ' ' . trans('certificates.student does not belong to this session'));
                        }
                    },

                ],
            "attributes.certificate_id" =>
                ["required",
                    "exists:thanking_certificates,id",
                    function ($attribute, $value, $fail) {
                        $attributes = $this->request->get('data')['attributes'];
                        $student = User::query()->findOrFail($attributes['student_id']);

                        $certificate = $student->certificates()
                            ->wherepivot('thanking_certificate_id', '=', $attributes['certificate_id'])
                            ->wherepivot('vcr_session_id', '=', $attributes['vcr_session_id'])
                            ->first();

                        if ($certificate) {
                            $fail($attribute . ' ' . trans('certificates.He already had this certificate'));
                        }
                    },

                ],
            "attributes.vcr_session_id" => [
                "required",
                "exists:vcr_sessions,id",
                function ($attribute, $value, $fail) {

                    $session = VCRSession::query()->findOrFail($value);
                    if ($session) {
                        if (!Carbon::now()->between(Carbon::parse($session->time_to_start), Carbon::parse($session->time_to_end))) {
                            $fail($attribute . ' ' . trans('certificates.you cannot give a certificate outside of the session time'));
                        }
                    }
                },
            ],

        ];
    }
}
