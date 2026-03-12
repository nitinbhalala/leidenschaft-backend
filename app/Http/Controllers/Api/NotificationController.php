<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Http\Request;
use Exception;

class NotificationController extends BaseController
{
    public function index()
    {
        try {
            $notifications = Notification::latest()->paginate(10);

            return $this->success(
                $notifications,
                'Notifications fetched successfully'
            );
        } catch (Exception $e) {
            return $this->error('Something went wrong', 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $notification = Notification::create([
                'user_id' => auth()->id() ?? null,
                'order_id' => $request->order_id ?? null,
                'payment_id' => $request->payment_id ?? null,
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type
            ]);

            return $this->success(
                $notification,
                'Notification created successfully'
            );
        } catch (Exception $e) {
            return $this->error('Something went wrong', 500);
        }
    }

    public function show($id)
    {
        try {
            $notification = Notification::find($id);

            if (!$notification) {
                return $this->error('Notification not found', 404);
            }

            return $this->success(
                $notification,
                'Notification fetched successfully'
            );
        } catch (Exception $e) {
            return $this->error('Something went wrong', 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $notification = Notification::find($id);

            if (!$notification) {
                return $this->error('Notification not found', 404);
            }

            $notification->update([
                'title' => $request->title ?? $notification->title,
                'message' => $request->message ?? $notification->message,
                'type' => $request->type ?? $notification->type,
                'is_read' => $request->is_read ?? $notification->is_read,
            ]);

            return $this->success(
                $notification,
                'Notification updated successfully'
            );
        } catch (Exception $e) {
            return $this->error('Something went wrong', 500);
        }
    }

    public function destroy($id = null, Request $request)
    {
        try {
            if ($request->clear_all == true) {
                Notification::truncate();

                return $this->success(
                    null,
                    'Notifications deleted successfully'
                );
            }

            $notification = Notification::findOrFail($id);
            $notification->delete();

            return $this->success(null, 'Notification deleted successfully');
        } catch (Exception $e) {

            return $this->error('Something went wrong', 500);
        }
    }

    public function read(Request $request, $id = null)
    {
        try {
            if ($request->read_all == true) {
                Notification::where('is_read', 0)->update([
                    'is_read' => 1,
                    'read_at' => now()
                ]);

                return $this->success(
                    null,
                    'All notifications marked as read'
                );
            }

            $notification = Notification::find($id);

            if (!$notification) {
                return $this->error('Notification not found', 404);
            }

            $notification->update([
                'is_read' => 1,
                'read_at' => now()
            ]);

            return $this->success(
                $notification,
                'Notification marked as read'
            );
        } catch (Exception $e) {
            return $this->error('Something went wrong', 500);
        }
    }
}
