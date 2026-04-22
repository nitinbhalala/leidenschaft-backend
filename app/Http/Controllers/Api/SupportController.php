<?php

namespace App\Http\Controllers\Api;

use App\Models\Support;
use App\Models\SupportChat;
use Illuminate\Http\Request;

class SupportController extends BaseController
{
    public function index(Request $request)
    {
        $query = Support::with('customer')->latest();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $supports = $query->paginate(10);

        $items = collect($supports->items())->map(function ($support) {
            $support->unread_count = SupportChat::where('support_id', $support->id)
                ->where('sender_type', 'customer')
                ->where('is_read', false)
                ->count();
            return $support;
        });

        return $this->success([
            'current_page' => $supports->currentPage(),
            'per_page'     => $supports->perPage(),
            'total'        => $supports->total(),
            'data'         => $items,
        ], 'Support tickets fetched successfully');
    }

    public function customerIndex(Request $request)
    {
        $customerId = $request->attributes->get('customer')->id;

        $supports = Support::where('customer_id', $customerId)
            ->latest()
            ->paginate(10);

        $items = collect($supports->items())->map(function ($support) {
            $support->unread_count = SupportChat::where('support_id', $support->id)
                ->where('sender_type', 'admin')
                ->where('is_read', 0)
                ->count();
            return $support;
        });

        return $this->success([
            'current_page' => $supports->currentPage(),
            'per_page'     => $supports->perPage(),
            'total'        => $supports->total(),
            'data'         => $items,
        ], 'Your support tickets fetched successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject'     => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'priority'    => 'required|in:low,medium,high',
            'description' => 'required|string',
        ]);

        $customerId = $request->attributes->get('customer')->id;

        $support = Support::create([
            'customer_id' => $customerId,
            'subject'     => $request->subject,
            'category'    => $request->category,
            'priority'    => $request->priority,
            'description' => $request->description,
        ]);

        return $this->success($support->load('customer'), 'Support ticket created successfully', 201);
    }

    public function show($id)
    {
        $support = Support::with(['customer', 'chats'])->find($id);

        if (!$support) {
            return $this->error('Support ticket not found', 404);
        }

        return $this->success($support, 'Support ticket fetched successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $support = Support::find($id);

        if (!$support) {
            return $this->error('Support ticket not found', 404);
        }

        $support->update(['status' => $request->status]);

        return $this->success($support, 'Support ticket status updated successfully');
    }
}
