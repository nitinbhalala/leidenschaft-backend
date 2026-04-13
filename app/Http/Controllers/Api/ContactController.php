<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Contact;
use App\Http\Requests\ContactRequest;
use Illuminate\Http\Request;

class ContactController extends BaseController
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

        $contacts->getCollection()->transform(function ($contact) {
            return $this->formatContact($contact);
        });

        return $this->success($contacts, "Contacts fetched successfully", 200);
    }

    public function store(ContactRequest $request)
    {
        $data = $request->validated();

        if (isset($data['contact_method']) && is_array($data['contact_method'])) {
            $data['contact_method'] = json_encode($data['contact_method']);
        }

        $contact = Contact::create($data);

        return $this->success(
            $this->formatContact($contact),
            "Message sent successfully",
            201
        );
    }

    public function show(Contact $contact)
    {
        return $this->success(
            $this->formatContact($contact),
            "Contact fetched successfully"
        );
    }

    public function update(ContactRequest $request, Contact $contact)
    {
        $data = $request->validated();

        if (isset($data['contact_method']) && is_array($data['contact_method'])) {
            $data['contact_method'] = json_encode($data['contact_method']);
        }

        $contact->update($data);

        return $this->success(
            $this->formatContact($contact),
            "Contact updated successfully"
        );
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return $this->success(null, "Contact deleted successfully");
    }

    public function stats()
    {
        $data = [
            'total_contacts'    => Contact::count(),
            'new_contacts'      => Contact::where('status', 'New')->count(),
            'replied_contacts'  => Contact::where('status', 'Replied')->count(),
            'archived_contacts' => Contact::where('status', 'Archived')->count(),
        ];

        return $this->success($data, "Contact stats fetched successfully");
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
