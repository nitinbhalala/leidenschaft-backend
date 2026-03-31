<?php

namespace App\Http\Controllers\Api;

use App\Models\EmailTemplate;
use App\Http\Requests\EmailTemplateRequest;
use Exception;

class EmailTemplateController extends BaseController
{
    public function index()
    {
        try {
            $query = EmailTemplate::query();

            $emailTemplates = $query->latest()->get();

            return $this->success($emailTemplates, 'Email templates fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(EmailTemplateRequest $request)
    {
        try {
            $admin = $request->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can create email templates.', 403);
            }

            $emailTemplate = EmailTemplate::create($request->validated());

            return $this->success($emailTemplate, 'Email template created successfully', 201);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $emailTemplate = EmailTemplate::find($id);

            if (!$emailTemplate) {
                return $this->error('Email template not found', 404);
            }

            return $this->success($emailTemplate, 'Email template fetched successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function update(EmailTemplateRequest $request, $id)
    {
        try {
            $admin = $request->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can update email templates.', 403);
            }

            $emailTemplate = EmailTemplate::find($id);

            if (!$emailTemplate) {
                return $this->error('Email template not found', 404);
            }

            $emailTemplate->update($request->validated());

            return $this->success($emailTemplate, 'Email template updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $admin = request()->attributes->get('admin');

            if (!$admin) {
                return $this->error('Unauthorized. Only admin can delete email templates.', 403);
            }

            $emailTemplate = EmailTemplate::find($id);

            if (!$emailTemplate) {
                return $this->error('Email template not found', 404);
            }

            $emailTemplate->delete();

            return $this->success(null, 'Email template deleted successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
