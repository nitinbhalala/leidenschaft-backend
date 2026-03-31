<?php

namespace App\Http\Controllers\Api;

use App\Models\OfferTemplate;
use App\Http\Requests\OfferTemplateRequest;
use Illuminate\Support\Facades\Storage;
use Exception;

class OfferTemplateController extends BaseController
{
    public function index()
    {
        try {
            $query = OfferTemplate::query();

            $admin = request()->attributes->get('admin');

            if (!$admin) {
                $query->where('status', 1);
            }

            $templates = $query->latest()->get();

            return $this->success($templates, 'Offer templates fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(OfferTemplateRequest $request)
    {
        try {
            $admin = $request->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can create offer templates.', 403);
            }

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('offer-templates', 'public');
            }

            $template = OfferTemplate::create($data);

            return $this->success($template, 'Offer template created successfully', 201);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $admin = request()->attributes->get('admin');

            $template = OfferTemplate::find($id);

            if (!$template) {
                return $this->error('Offer template not found', 404);
            }

            if (!$admin && !$template->status) {
                return $this->error('Offer template not found', 404);
            }

            return $this->success($template, 'Offer template fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function update(OfferTemplateRequest $request, $id)
    {
        try {
            $admin = $request->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can update offer templates.', 403);
            }

            $data = $request->validated();

            $template = OfferTemplate::find($id);

            if (!$template) {
                return $this->error('Offer template not found', 404);
            }

            if ($request->hasFile('image')) {
                $rawImage = $template->getRawOriginal('image');

                if ($rawImage && Storage::disk('public')->exists($rawImage)) {
                    Storage::disk('public')->delete($rawImage);
                }

                $data['image'] = $request->file('image')->store('offer-templates', 'public');
            }

            $template->update($data);

            return $this->success($template, 'Offer template updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $admin = request()->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can update offer template status.', 403);
            }

            $template = OfferTemplate::find($id);

            if (!$template) {
                return $this->error('Offer template not found', 404);
            }

            $template->update([
                'status' => !$template->status
            ]);

            return $this->success($template, 'Offer template status updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $admin = request()->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can delete offer templates.', 403);
            }

            $template = OfferTemplate::find($id);

            if (!$template) {
                return $this->error('Offer template not found', 404);
            }

            $rawImage = $template->getRawOriginal('image');

            if ($rawImage && Storage::disk('public')->exists($rawImage)) {
                Storage::disk('public')->delete($rawImage);
            }

            $template->delete();

            return $this->success(null, 'Offer template deleted successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
