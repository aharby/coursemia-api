<?php
namespace App\OurEdu\Subjects\UseCases\EditResource;

interface EditResourceSubjectFormatSubjectUseCaseInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function editResourceContent(int $resourceSubjectFormatId, $data);
}
