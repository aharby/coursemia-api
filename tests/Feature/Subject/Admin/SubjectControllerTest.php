<?php

namespace Tests\Feature;

use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubjectControllerTest extends TestCase
{
    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */

    public function test_list_subjects()
    {
        dump('test_list_subjects');
        $this->authAdmin();
        $response = $this
            ->withSession(['locale' => 'en'])
            ->get(route('admin.subjects.get.index'))
            ->assertStatus(200);
    }

    public function test_create_subjects()
    {
        dump('test_create_subjects');
        $this->authAdmin();
        
        factory(GradeClass::class)->create();

        $data = [
            'name' => 'name name',
            'country_id' => Country::first()->id,
            'start_date' => '2019-07-22',
            'end_date' => '2019-10-04',
            'subscription_cost' => 1,
            'educational_system_id' => EducationalSystem::first()->id,
            'grade_class_id' => GradeClass::first()->id,
            'educational_term_id' => Option::where('type', OptionsTypes::EDUCATIONAL_TERM)->first()->id,
            'academical_years_id' => Option::where('type', OptionsTypes::ACADEMIC_YEAR)->first()->id,

            'is_active' => 1,
        ];


        $response = $this
            ->post(route('admin.subjects.post.create'), $data);

        $this->assertDatabaseHas('subjects', [
            'name' => $data['name'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'educational_system_id' => $data['educational_system_id'],
            'educational_term_id' => $data['educational_term_id'],
            'academical_years_id' => $data['academical_years_id'],
            'grade_class_id' => $data['grade_class_id'],
            'is_active' => $data['is_active'],
            'country_id' => $data['country_id'],
        ]);
    }


    public function test_edit_subjects()
    {
        dump('test_edit_subjects');
        $this->authAdmin();
        
        $record = factory(Subject::class)->create();
        $row = factory(Subject::class)->make();
        $name = [
          'name'=>'new name'
        ];
        $row = array_merge($row->toArray(), $name, ['is_active'=>true]);
        $response = $this
            ->put(route('admin.subjects.put.edit', $record->id), $row);

        $record = Subject::find($record->id);
        $this->assertEquals($row['name'], $record->name);
    }

    public function test_delete_subjects()
    {
        dump('test_delete_subjects');
        $this->authAdmin();
        $record = Subject::create(factory(Subject::class)->make()->toArray());
        $response = $this
            ->withSession(['locale' => 'en'])
            ->delete(route('admin.subjects.delete', $record->id));

        $this->assertSoftDeleted('subjects', [
            'id' => $record->id,


            ]);

        $response->assertStatus(302);
    }

    public function test_view_subject_tasks()
    {
        dump('test_view_subject_tasks');
        $this->authAdmin();
        $subject = factory(Subject::class)->create();
        $task = factory(Task::class)->create(['title' => 'title']);
        $response = $this
            ->get('/admin/subjects/tasks/'.$subject->id)
            ->assertSee('title');
    }

    public function test_pause_unpause_subject()
    {
        dump('test_pause_unpause_subject');
        $this->authAdmin();
        
        $activeSubject = factory(Subject::class)->create([
            'is_active' => 1
        ]);
        $this->post('/admin/subjects/pause/'.$activeSubject->id);
        $this->assertDatabaseHas('subjects', [
                 'is_active' => 0
             ]);

        $pausedSubject = factory(Subject::class)->create([
            'is_active' => 0
        ]);
        $this->post('/admin/subjects/pause/'.$pausedSubject->id);
        $this->assertDatabaseHas('subjects', [
                 'is_active' => 1
             ]);
    }
}
