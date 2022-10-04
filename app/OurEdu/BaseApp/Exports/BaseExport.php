<?php

namespace App\OurEdu\BaseApp\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BaseExport implements FromCollection, WithHeadings
{

    private $collection;

    private $heading;


    public function __construct(Collection $collection, array $heading = [])
    {
        $this->collection = $collection;
        $this->heading = $heading;
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->collection;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return $this->heading;
    }

}
