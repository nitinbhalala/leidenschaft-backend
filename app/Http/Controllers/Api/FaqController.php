<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Faq;
use App\Http\Requests\FaqRequest;
use Exception;

class FaqController extends BaseController
{
    public function index()
    {
        try {
            $query = Faq::latest();

            $admin = request()->attributes->get('admin');

            if (!$admin) {
                $query->where('status', 1);
            }

            $faqs = $query->get();

            return $this->success($faqs, "Faq list fetched successfully");
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(FaqRequest $request)
    {
        try {
            $admin = $request->attributes->get('admin');

            if (!$admin) {
                return $this->error("Unauthorized. Only admin can create faq.", 403);
            }

            $faq = Faq::create([
                'question' => $request->question,
                'answer' => $request->answer,
                'status' => $request->status,
            ]);

            return $this->success($faq, "Faq created successfully");
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $faq = Faq::find($id);

            if (!$faq) {
                return $this->error("Faq not found", 404);
            }

            $admin = request()->attributes->get('admin');

            if (!$admin && !$faq->status) {
                return $this->error("Faq not found", 404);
            }

            return $this->success($faq, "Faq fetched successfully");
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function update(FaqRequest $request, $id)
    {
        try {
            $admin = $request->attributes->get('admin');

            if (!$admin) {
                return $this->error("Unauthorized. Only admin can update faq.", 403);
            }

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
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $admin = request()->attributes->get('admin');

            if (!$admin) {
                return $this->error("Unauthorized. Only admin can delete faq.", 403);
            }

            $faq = Faq::find($id);

            if (!$faq) {
                return $this->error("Faq not found", 404);
            }

            $faq->delete();

            return $this->success(null, "Faq deleted successfully");
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
