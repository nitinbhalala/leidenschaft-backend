<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Faq;
use App\Http\Requests\FaqRequest;

class FaqController extends BaseController
{
    public function index()
    {
        $faqs = Faq::latest()->get();

        return $this->success($faqs, "Faq list fetched successfully");
    }

    public function store(FaqRequest $request)
    {
        $faq = Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'status' => $request->status,
        ]);

        return $this->success($faq, "Faq created successfully");
    }

    public function show($id)
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return $this->error("Faq not found", 404);
        }

        return $this->success($faq, "Faq fetched successfully");
    }

    public function update(FaqRequest $request, $id)
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return $this->error("Faq not found", 404);
        }

        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'status' => $request->status,
        ]);

        return $this->success($faq, "Faq updated successfully");
    }

    public function destroy($id)
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return $this->error("Faq not found", 404);
        }

        $faq->delete();

        return $this->success(null, "Faq deleted successfully");
    }
}
