<?php
declare(strict_types=1);

namespace App\OurEdu\Contact\Repository;

interface ContactInterface
{

    public function get();
    public function create($data);
    public function paginate($perPage = 10);
    public function findOrFail($id);

}
