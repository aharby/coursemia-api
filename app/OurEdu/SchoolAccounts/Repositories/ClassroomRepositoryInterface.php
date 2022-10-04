<?php


namespace App\OurEdu\SchoolAccounts\Repositories;


use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ClassroomRepositoryInterface
{
    public function find($id): ?Classroom;

    public function getBranchClassroomClasses($brachId);

    public function classPresenceReport(Request $request, SchoolAccountBranch $branch = null) : LengthAwarePaginator;
    /**
     * @param $id
     * @return Classroom
     */

    public function listClassroomsNamesIDs(int $branch);

    public function getBranchClassroomsByIds($classroomIds,$branchId,$subjectId);

    public function teacherAttendance(SchoolAccount $schoolAccount, array $data = []);

    public function getClassroomsByBranchAndGradeclass($branchId,$gradeClassId,$subjectId=null);

    public function getClassroomsByBranchesAndGradeClasses(array $branches, int $gradeClassId, int $subjectId = null, int $educationalSystemId = null);
}
