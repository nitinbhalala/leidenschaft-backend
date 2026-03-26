<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends BaseController
{
    public function show(Request $request): JsonResponse
    {
        $admin = $request->attributes->get('admin');

        if (!$admin) {
            return $this->error('Unauthorized.', 401);
        }

        return $this->success([
            'id'        => $admin->id,
            'firstName' => $admin->first_name ?? explode(' ', $admin->name)[0],
            'lastName'  => $admin->last_name ?? (explode(' ', $admin->name)[1] ?? ''),
            'email'     => $admin->email,
            'phone'     => $admin->phone ?? '',
            'avatar'    => $admin->avatar ?? null,
            'createdAt' => $admin->created_at?->toDateTimeString(),
            'updatedAt' => $admin->updated_at?->toDateTimeString(),
        ], 'Profile fetched successfully.');
    }

    public function update(ProfileRequest $request): JsonResponse
    {
        $admin = $request->attributes->get('admin');

        if (!$admin) {
            return $this->error('Unauthorized.', 401);
        }

        try {
            $updateData = [
                'email' => $request->email,
                'phone' => $request->phone,
            ];

            // Name handling
            if (in_array('first_name', $admin->getFillable())) {
                $updateData['first_name'] = $request->firstName;
                $updateData['last_name']  = $request->lastName;
            } else {
                $updateData['name'] = trim($request->firstName . ' ' . $request->lastName);
            }

            if ($request->hasFile('avatar')) {

                $oldAvatar = $admin->getRawOriginal('avatar');
                if ($oldAvatar) {
                    Storage::disk('public')->delete($oldAvatar);
                }

                $path = $request->file('avatar')->store('avatars', 'public');

                $updateData['avatar'] = $path;
            }

            $admin->update($updateData);

            return $this->success([
                'id'        => $admin->id,
                'firstName' => $request->firstName,
                'lastName'  => $request->lastName,
                'email'     => $admin->email,
                'phone'     => $admin->phone ?? '',
                'avatar'    => $admin->avatar ?? null,
                'createdAt' => $admin->created_at?->toDateTimeString(),
                'updatedAt' => $admin->updated_at?->toDateTimeString(),
            ], 'Profile updated successfully.');
        } catch (\Exception $e) {
            return $this->error('Error updating profile', 500, $e->getMessage());
        }
    }

    public function changePassword(Request $request): JsonResponse
    {
        $admin = $request->attributes->get('admin');

        if (!$admin) {
            return $this->error('Unauthorized.', 401);
        }

        $request->validate([
            'currentPassword' => 'required',
            'newPassword'     => 'required|min:6',
        ]);

        if (!Hash::check($request->currentPassword, $admin->password)) {
            return $this->error('Current password is incorrect.', 400);
        }

        $admin->update([
            'password' => Hash::make($request->newPassword),
        ]);

        return $this->success([], 'Password changed successfully.');
    }
}
