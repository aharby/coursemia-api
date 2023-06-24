<?php

namespace App\Modules\FAQs\Controllers;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\FAQs\Resources\Api\FAQResource;
use App\Modules\FAQs\Models\FAQs;
use Illuminate\Http\Request;

class FAQAdminApiController extends Controller
{
    public function index()
    {
        $faqs = FAQs::query();
        $faqs = $faqs->filter()->sorter();
        $faqs = $faqs->paginate(request()->perPage, ['*'], 'page', request()->page);
        return response()->json([
            'total' => $faqs->total(),
            'faqs' => FAQResource::collection($faqs->items())
        ]);
    }

    public function show($id)
    {
        $faq = FAQs::findOrFail($id);
        if ($faq){
            return customResponse(new FAQResource($faq), '', 200,1);
        };
        return customResponse('', trans('api.no faq found'),  404,2);
    }

    public function store(Request $request)
    {
        $faq = new FAQs;
        $faq->{'question:en'} = $request->question_en;
        $faq->{'question:ar'} = $request->question_ar;
        $faq->{'answer:en'} = $request->answer_en;
        $faq->{'answer:ar'} = $request->answer_ar;
        $faq->save();
        return customResponse(new FAQResource($faq), "", 200,StatusCodesEnum::DONE);
    }

    public function update(Request $request, $id)
    {
        $faq = FAQs::find($id);
        $faq->{'question:en'} = $request->question_en;
        $faq->{'question:ar'} = $request->question_ar;
        $faq->{'answer:en'} = $request->answer_en;
        $faq->{'answer:ar'} = $request->answer_ar;
        $faq->save();
        return customResponse(new FAQResource($faq), '', 200,StatusCodesEnum::DONE);

    }

    public function destroy($id)
    {
        FAQs::where('id', $id)->delete();
        return customResponse((object)[], '', 200,StatusCodesEnum::DONE);
    }

}
