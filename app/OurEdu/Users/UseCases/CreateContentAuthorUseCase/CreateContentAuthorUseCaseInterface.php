<?php

namespace App\OurEdu\Users\UseCases\CreateContentAuthorUseCase;

use App\OurEdu\Users\Models\ContentAuthor;

interface CreateContentAuthorUseCaseInterface
{
    public function CreateContentAuthor(array $data): ?ContentAuthor;
}