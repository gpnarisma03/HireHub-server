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
        'resume_file' => 'required|file|mimes:pdf,doc,docx|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $userId = Auth::id();

    // ✅ Prevent duplicate application
    $existingApplication = Application::where('job_id', $request->job_id)
        ->where('user_id', $userId)
        ->first();

    if ($existingApplication) {
        return response()->json([
            'success' => false,
            'message' => 'You have already applied for this job.',
        ], 409); // Conflict
    }

    // ✅ Store resume
    $path = $request->file('resume_file')->store('resumes', 'public');

    // ✅ Create new application
    $application = Application::create([
        'job_id' => $request->job_id,
        'user_id' => $userId,
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
    $applications = Application::with('job.company') // Load both job and job's company
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
