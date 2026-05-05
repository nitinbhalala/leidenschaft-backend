<?php

namespace App\Http\Controllers\Api;

use App\Models\ErrorLog;
use Illuminate\Http\Request;
use Exception;

class ErrorLogController extends BaseController
{
    public function index()
    {
        try {
            $logs = ErrorLog::latest()->paginate(20);

            return $this->success($logs);
        } catch (Exception $e) {
            return $this->error('Something went wrong', 500);
        }
    }

    public function show($id)
    {
        try {
            $log = ErrorLog::findOrFail($id);

            return $this->success($log);
        } catch (Exception $e) {
            return $this->error('Log not found', 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $log = ErrorLog::create([
                'source' => $request->source,
                'user' => $request->user,
                'message' => $request->message,
                'stack_trace' => $request->stack_trace,
                'status' => 'open'
            ]);

            return $this->success($log, 'Error logged successfully', 201);
        } catch (Exception $e) {
            return $this->error('Failed to log error', 500);
        }
    }

    public function markResolved($id)
    {
        try {
            $log = ErrorLog::findOrFail($id);

            $log->update([
                'status' => 'resolved'
            ]);

            return $this->success($log, 'Marked as resolved');
        } catch (Exception $e) {
            return $this->error('Something went wrong', 500);
        }
    }

    public function destroy($id = null, Request $request)
    {
        try {
            if ($request->has('ids')) {

                ErrorLog::whereIn('id', $request->ids)->delete();

                return $this->success(null, 'Error logs deleted successfully');
            }

            $log = ErrorLog::findOrFail($id);

            $log->delete();

            return $this->success(null, 'Error log deleted successfully');
        } catch (Exception $e) {

            return $this->error('Something went wrong', 500);
        }
    }
}
