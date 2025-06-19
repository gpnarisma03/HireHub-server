<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    // Job seeker applies for a job
    public function apply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|exists:jobs,job_id',
            'cover_letter' => 'required|string',
            'resume_file' => 'required|file|mimes:pdf,doc,docx|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $path = $request->file('resume_file')->store('resumes', 'public');

        $application = Application::create([
            'job_id' => $request->job_id,
            'user_id' => Auth::id(),
            'cover_letter' => $request->cover_letter,
            'resume_file' => $path,
            'applied_at' => now(),
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully.',
            'application' => $application,
        ], 201);
    }

    // Get logged-in user's applications
    public function myApplications()
    {
        $applications = Application::with('job')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json($applications);
    }

    // (Optional) Get all applications for a specific job (for admin/employer)
    public function getByJob($job_id)
    {
        $applications = Application::with('user', 'job')
            ->where('job_id', $job_id)
            ->get();

        return response()->json($applications);
    }

    // Admin/Employer can update application status
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,reviewed,hired,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $application = Application::findOrFail($id);
        $application->status = $request->status;
        $application->save();

        return response()->json(['success' => true, 'message' => 'Application status updated.']);
    }
}
