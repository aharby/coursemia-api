<?php

namespace App\Modules\FAQs\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQs extends Model
{
    use HasFactory, Translatable;
    protected $table = 'faqs';

    protected $translationForeignKey = "faqs_id";
    protected $translatedAttributes = [
        'question',
        'answer'
    ];

    public function getTranslatedQuestionAttribute()
    {
        return $this->translate(app()->getLocale())->question;
    }
    public function getTranslatedAnswerAttribute()
    {
        return $this->translate(app()->getLocale())->answer;
    }

    public function ScopeFilter($query)
    {
        $query->when(request()->get('q') != '', function ($query) {
                $query->where(function ($q) {
                    $q->orWhereTranslationLike('question', '%' . request()->get('q') . '%');
                });
            });
    }

    public function ScopeSorter($query)
    {
        $query->when(request()->has('sortBy'), function ($quer) {
            $sortByDir = request()->get('sortDesc') == 'true' ? "desc" : "asc";
            switch (request()->get('sortBy')) {
                case 'question_en':
                    $quer->orderByTranslation('question', $sortByDir);
                    break;
                default:
                    $quer->orderBy('id', $sortByDir);
            }
        });
    }
}
