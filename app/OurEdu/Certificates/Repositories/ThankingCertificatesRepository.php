<?php


namespace App\OurEdu\Certificates\Repositories;

    use App\OurEdu\Certificates\Models\ThankingCertificate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ThankingCertificatesRepository implements ThankingCertificatesRepositoryInterface
{

    /**
     * @param array $data
     * @return Builder|Model
     */
    public function create(array $data)
    {
        return ThankingCertificate::query()->create($data);
    }

    public function update(ThankingCertificate $certificate, array $data)
    {
        if ($certificate->update($data)) {
            return $certificate->refresh();
        }

        return null;
    }
    public function all()
    {
        return ThankingCertificate::query()->orderBy('id', 'DESC');
    }
}
