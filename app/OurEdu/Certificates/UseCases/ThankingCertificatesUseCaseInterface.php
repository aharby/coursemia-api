<?php


namespace App\OurEdu\Certificates\UseCases;


use App\OurEdu\Certificates\Models\ThankingCertificate;

interface ThankingCertificatesUseCaseInterface
{
    /**
     * @param ThankingCertificate $certificate
     * @param array $data
     */
    public function printCertificate(ThankingCertificate $certificate, array $data);

    public function getExtension(string $imageName);
}
