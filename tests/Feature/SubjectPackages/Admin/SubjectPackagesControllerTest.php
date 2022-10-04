<?php

namespace Tests\Feature;

use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\OurEdu\SubjectPackages\Package;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SubjectPackagesControllerTest extends TestCase
{
    public function test_list_subject_packages()
    {
        dump('test_list_subject_packages');
        $this->authAdmin();
        $response = $this
            ->withSession(['locale' => 'en'])
            ->get(route('admin.subjectPackages.get.index'));
        $response->assertOk();
    }

    public function test_create_subject_package()
    {
        dump('test_create_subject_package');
        $this->authAdmin();


        $data = [
            'name' => 'name name',
            'price' => '1000',
            'description' => 'description description',
            'educational_system_id' => EducationalSystem::first()->id ?? factory(EducationalSystem::class)->create()->id,
            'country_id' => Country::first()->id ?? factory(Country::class)->create()->id,
            'grade_class_id' =>  GradeClass::first()->id ?? factory(GradeClass::class)->create()->id,
            'academical_years_id' =>  Option::where('type', OptionsTypes::ACADEMIC_YEAR)->first()->id ?? factory(Option::class)->create(['type' => OptionsTypes::ACADEMIC_YEAR])->id,
            'subject_id' => 5,
            'is_active' => 1,
            'picture' => UploadedFile::fake()->image('avatar.jpg'),
        ];

        $response = $this
            ->post(route('admin.subjectPackages.post.create'), $data);
        $this->assertDatabaseHas('subject_packages', [
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description'],
            'educational_system_id' => $data['educational_system_id'],
            'academical_years_id' => $data['academical_years_id'],
            'grade_class_id' => $data['grade_class_id'],
            'is_active' => $data['is_active'],
            'country_id' => $data['country_id'],
        ]);
    }


    public function test_edit_subject_package()
    {
        dump('test_edit_subject_package');
        $this->authAdmin();

        $record = factory(Package::class)->create();
        $row = factory(Package::class)->make();
        $name = [
            'name'=>'new name'
        ];
        $row = array_merge($row->toArray(), $name, ['is_active'=>true]);
        $response = $this
            ->put(route('admin.subjectPackages.put.edit', $record->id), $row);

        $record = Package::find($record->id);
        $this->assertEquals($row['name'], $record->name);
    }

    public function test_delete_subject_package()
    {
        dump('test_delete_subject_package');
        $this->authAdmin();
        $record = Package::create(factory(Package::class)->make()->toArray());
        $response = $this
            ->withSession(['locale' => 'en'])
            ->delete(route('admin.subjectPackages.delete', $record->id));
        $response->assertStatus(302);
    }
}
