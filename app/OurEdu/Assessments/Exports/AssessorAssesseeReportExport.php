<?php


namespace App\OurEdu\Assessments\Exports;


use App\OurEdu\BaseApp\Exports\BaseExport;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AssessorAssesseeReportExport extends BaseExport implements WithMapping, ShouldAutoSize
{

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($assessee): array
    {
        $scorePercentage = $assessee->total_mark > 0 ? ($assessee->score/$assessee->total_mark)*100:0;

        return [
            'name' => (string)$assessee->assessee->name,
            'score' => (float)number_format($scorePercentage, 2),
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('assessment.assessee_name'),
            trans('assessment.avg_score'),
        ];
    }

}
