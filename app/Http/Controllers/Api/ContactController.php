<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Http\Requests\ContactRequest;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::latest();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($request->status && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        $perPage = $request->per_page ?? 10;
        $contacts = $query->paginate($perPage);

        // Transform contact_method from JSON string to array
        $contacts->getCollection()->transform(function ($contact) {
            return $this->formatContact($contact);
        });

        return response()->json([
            'success' => true,
            'data' => $contacts
        ]);
    }

    public function store(ContactRequest $request)
    {
        $data = $request->validated();

        // Store contact_method as JSON
        if (isset($data['contact_method']) && is_array($data['contact_method'])) {
            $data['contact_method'] = json_encode($data['contact_method']);
        }

        $contact = Contact::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $this->formatContact($contact)
        ], 201);
    }

    public function show(Contact $contact)
    {
        return response()->json([
            'success' => true,
            'data' => $this->formatContact($contact)
        ]);
    }

    public function update(ContactRequest $request, Contact $contact)
    {
        $data = $request->validated();

        if (isset($data['contact_method']) && is_array($data['contact_method'])) {
            $data['contact_method'] = json_encode($data['contact_method']);
        }

        $contact->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Contact updated successfully',
            'data' => $this->formatContact($contact)
        ]);
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully'
        ]);
    }

    public function stats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_contacts'   => Contact::count(),
                'new_contacts'     => Contact::where('status', 'New')->count(),
                'replied_contacts' => Contact::where('status', 'Replied')->count(),
                'archived_contacts' => Contact::where('status', 'Archived')->count(),
            ]
        ]);
    }

    private function formatContact(Contact $contact): array
    {
        return [
            'id'            => $contact->id,
            'firstName'     => $contact->first_name,
            'lastName'      => $contact->last_name,
            'email'         => $contact->email,
            'phone'         => $contact->phone,
            'contactMethod' => is_string($contact->contact_method)
                ? json_decode($contact->contact_method, true) ?? []
                : ($contact->contact_method ?? []),
            'subject'       => $contact->subject,
            'message'       => $contact->message,
            'status'        => $contact->status,
            'date'          => $contact->created_at?->format('M d, Y'),
        ];
    }
}
