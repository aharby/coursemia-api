<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\DragDrop;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DragDropOption extends BaseModel
{
    use HasFactory;

    protected $table = 'res_drag_drop_options';

    protected $fillable = [
        'option',
        'res_drag_drop_data_id',
        'drag_drop_type',

    ];
}
