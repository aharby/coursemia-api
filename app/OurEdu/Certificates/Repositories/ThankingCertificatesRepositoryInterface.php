<?php


namespace App\OurEdu\Certificates\Repositories;


use App\OurEdu\Certificates\Models\ThankingCertificate;

interface ThankingCertificatesRepositoryInterface
{
    public function create(array $data);

    public function update(ThankingCertificate $certificate, array $data);

    public function all();
}
