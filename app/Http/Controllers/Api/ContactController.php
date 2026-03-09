<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Http\Requests\ContactRequest;

class ContactController extends Controller
{
    // List contacts
    public function index()
    {
        $contacts = Contact::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $contacts
        ]);
    }

    // Store contact message
    public function store(ContactRequest $request)
    {
        $contact = Contact::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $contact
        ], 201);
    }

    // Show single contact
    public function show(Contact $contact)
    {
        return response()->json([
            'success' => true,
            'data' => $contact
        ]);
    }

    // Update contact
    public function update(ContactRequest $request, Contact $contact)
    {
        $contact->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Contact updated successfully',
            'data' => $contact
        ]);
    }

    // Delete contact
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully'
        ]);
    }
}
