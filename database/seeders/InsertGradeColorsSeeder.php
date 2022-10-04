<?php

namespace Database\Seeders;

use App\OurEdu\GradeColors\Models\GradeColor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class InsertGradeColorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $files = File::allFiles(storage_path("app/public/grades_color"));

        foreach ($files as $file) {
            $fileOriginName = $file->getBasename("." . $file->getExtension());
            $gradeColor = GradeColor::query()->where("slug", "=", $fileOriginName)->first();

            if ($gradeColor) {
                continue;
            }

            $data = [
                "slug" => $fileOriginName,
                "image" => "grades_color/" . $file->getBasename(),
            ];

            GradeColor::query()->create($data);
        }
    }
}
