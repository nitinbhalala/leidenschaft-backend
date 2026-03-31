<?php

namespace App\Http\Controllers\Api;

use App\Models\Offer;
use App\Http\Requests\OfferRequest;
use Illuminate\Support\Facades\Storage;
use Exception;

class OfferController extends BaseController
{
    public function index()
    {
        try {
            $query = Offer::query();

            $admin = request()->attributes->get('admin');

            if (!$admin) {
                $query->where('status', 1);
            }

            $offers = $query->latest()->get();

            return $this->success($offers, 'Offers fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(OfferRequest $request)
    {
        try {
            $admin = $request->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can create offers.', 403);
            }

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('offers', 'public');
            }

            $offer = Offer::create($data);

            return $this->success($offer, 'Offer created successfully', 201);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function show(Offer $offer)
    {
        try {
            $admin = request()->attributes->get('admin');

            if (!$admin && !$offer->status) {
                return $this->error('Offer not found', 404);
            }

            return $this->success($offer, 'Offer fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function update(OfferRequest $request, $id)
    {
        try {
            $admin = $request->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can update offers.', 403);
            }

            $data = $request->validated();

            $offer = Offer::find($id);

            if (!$offer) {
                return $this->error('Offer not found', 404);
            }

            if ($request->hasFile('image')) {
                $rawImage = $offer->getRawOriginal('image');

                if ($rawImage && Storage::disk('public')->exists($rawImage)) {
                    Storage::disk('public')->delete($rawImage);
                }

                $data['image'] = $request->file('image')->store('offers', 'public');
            }

            $offer->update($data);

            return $this->success($offer, 'Offer updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $admin = request()->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can update offer status.', 403);
            }

            $offer = Offer::find($id);

            if (!$offer) {
                return $this->error('Offer not found', 404);
            }

            $offer->update([
                'status' => !$offer->status
            ]);

            return $this->success($offer, 'Offer status updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $admin = request()->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can delete offers.', 403);
            }

            $offer = Offer::find($id);

            if (!$offer) {
                return $this->error('Offer not found', 404);
            }

            $rawImage = $offer->getRawOriginal('image');

            if ($rawImage && Storage::disk('public')->exists($rawImage)) {
                Storage::disk('public')->delete($rawImage);
            }

            $offer->delete();

            return $this->success(null, 'Offer deleted successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
