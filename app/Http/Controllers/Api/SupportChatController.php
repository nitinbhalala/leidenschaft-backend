<?php

namespace App\Http\Controllers\Api;

use App\Models\Support;
use App\Models\SupportChat;
use Illuminate\Http\Request;

class SupportChatController extends BaseController
{
    public function indexAsAdmin($supportId)
    {
        $support = Support::find($supportId);

        if (!$support) {
            return $this->error('Support ticket not found', 404);
        }

        SupportChat::where('support_id', $supportId)
            ->where('sender_type', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $chats = SupportChat::where('support_id', $supportId)
            ->oldest()
            ->get();

        return $this->success([
            'support' => $support->load('customer'),
            'chats'   => $chats,
        ], 'Chat messages fetched successfully');
    }

    public function indexAsCustomer(Request $request, $supportId)
    {
        $customer = $request->attributes->get('customer');

        $support = Support::where('id', $supportId)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$support) {
            return $this->error('Support ticket not found or access denied', 404);
        }

        SupportChat::where('support_id', $supportId)
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $chats = SupportChat::where('support_id', $supportId)
            ->oldest()
            ->get();

        return $this->success([
            'support' => $support,
            'chats'   => $chats,
        ], 'Chat messages fetched successfully');
    }

    public function sendAsCustomer(Request $request, $supportId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $customer = $request->attributes->get('customer');

        $support = Support::where('id', $supportId)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$support) {
            return $this->error('Support ticket not found or access denied', 404);
        }

        if (in_array($support->status, ['resolved', 'closed'])) {
            return $this->error('Cannot send message on a resolved or closed ticket', 422);
        }

        $chat = SupportChat::create([
            'support_id'  => $supportId,
            'sender_id'   => $customer->id,
            'sender_type' => 'customer',
            'message'     => $request->message,
            'is_read'     => false,
        ]);

        return $this->success($chat, 'Message sent successfully', 201);
    }

    public function sendAsAdmin(Request $request, $supportId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $admin = $request->attributes->get('admin');

        $support = Support::find($supportId);

        if (!$support) {
            return $this->error('Support ticket not found', 404);
        }

        if (in_array($support->status, ['resolved', 'closed'])) {
            return $this->error('Cannot send message on a resolved or closed ticket', 422);
        }

        if ($support->status === 'open') {
            $support->update(['status' => 'in_progress']);
        }

        $chat = SupportChat::create([
            'support_id'  => $supportId,
            'sender_id'   => $admin->id,
            'sender_type' => 'admin',
            'message'     => $request->message,
            'is_read'     => false,
        ]);

        return $this->success($chat, 'Message sent successfully', 201);
    }

    public function markAllAsRead($supportId)
    {
        $support = Support::find($supportId);

        if (!$support) {
            return $this->error('Support ticket not found', 404);
        }

        $updated = SupportChat::where('support_id', $supportId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $this->success(
            ['marked_read' => $updated],
            'All messages marked as read successfully'
        );
    }

    public function unreadCount($supportId)
    {
        $support = Support::find($supportId);

        if (!$support) {
            return $this->error('Support ticket not found', 404);
        }

        $count = SupportChat::where('support_id', $supportId)
            ->where('sender_type', 'customer')
            ->where('is_read', false)
            ->count();

        return $this->success(['unread_count' => $count], 'Unread count fetched successfully');
    }
}
