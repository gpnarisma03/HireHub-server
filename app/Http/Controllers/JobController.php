<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:employer'])->only(['store', 'destroy']);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id'           => 'required|exists:companies,company_id',
            'category_id'          => 'required|exists:job_categories,category_id',
            'job_title'            => 'required|string|max:255',
            'job_description'      => 'required|string',
            'job_qualifications'   => 'required|string',
            'job_responsibilities' => 'required|string',
            'job_type'             => 'required|in:full-time,part-time',
            'job_vacancy'          => 'required|integer|min:1',
            'payment_range'        => 'required|string|max:255',
            'status'               => 'required|in:open,closed',
            'date_start'           => 'required|date',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $job = Job::create(array_merge(
            $validator->validated(),
            ['posted_at' => now()]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Job posted successfully',
            'job' => $job,
        ], 201);
    }

public function index()
{
$jobs = Job::with(['company', 'category'])
    ->where('status', 'open') // ✅ filter here
    ->orderBy('posted_at', 'desc')
    ->get();


    $jobs->map(function ($job) {
        $job->category->open_vacancy_total = $job->category
            ->jobs()
            ->where('status', 'open')
            ->sum('job_vacancy');
        return $job;
    });

    return response()->json([
        'success' => true,
        'jobs' => $jobs, // ✅ return the modified $jobs
    ]);
}



    public function destroy($id)
    {
        $job = Job::findOrFail($id);
        $job->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job deleted successfully',
        ]);
    }
}
