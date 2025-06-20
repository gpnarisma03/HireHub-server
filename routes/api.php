<?php

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ApplicationController;


Route::get('/ping', function () {
    return 'pong';
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/**------------------------USER ROUTES-------------------------------------- */
Route::prefix('users')->group(function () {
    Route::post('/', [UserController::class, 'insertUser']);
    Route::middleware('auth:sanctum')->get('/', [UserController::class, 'getUserDetails']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware(['auth:sanctum', 'role:admin'])->get('/all', [UserController::class, 'getAllUsers']);

});


/**------------------------EMAIL VERIFICATION ROUTES-------------------------------------- */

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Invalid hash'], 403);
    }

    if (! $request->hasValidSignature()) {
        return response()->json(['message' => 'Invalid or expired signature'], 403);
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    return response()->json([
        'success' => true,
        'message' => 'Email verified successfully!',
    ]);
})->name('verification.verify');


/**------------------------RESEND  EMAIL VERIFY ROUTES-------------------------------------- */
Route::post('/email/resend', function (Request $request) {
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);

    $user = User::where('email', $request->email)->first();

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email is already verified.'], 400);
    }

    $user->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification link resent!']);
});



/**------------------------COMPANY ROUTES-------------------------------------- */

Route::prefix('company')->middleware(['auth:sanctum', 'role:employer'])->group(function () {
    Route::post('/', [CompanyController::class, 'createCompany']);
Route::get('/{id}', [CompanyController::class, 'getCompanyById']);
    Route::delete('/{id}', [CompanyController::class, 'deleteCompany']);


});

/**------------------------JOB CATEGORY ROUTES-------------------------------------- */
Route::prefix('jobCategory')->group(function () {
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('/', [JobCategoryController::class, 'addCategory']);         // Create
        Route::put('/{id}', [JobCategoryController::class, 'updateCategory']);    // Update
        Route::delete('/{id}', [JobCategoryController::class, 'deleteCategory']); // Delete
    });
    Route::get('/', [JobCategoryController::class, 'getAllCategories']);     // Get all
});


/**------------------------JOB ROUTES-------------------------------------- */
Route::prefix('jobs')->group(function () {
    Route::get('/', [JobController::class, 'index']); // All users can view jobs

    Route::middleware(['auth:sanctum', 'role:employer'])->group(function () {
        Route::post('/', [JobController::class, 'store']);     // Add job (employer only)
        Route::delete('/{id}', [JobController::class, 'destroy']); // Delete job
    });
});



/**------------------------JOB APPLICATION ROUTES-------------------------------------- */
Route::prefix('applications')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [ApplicationController::class, 'apply']);           // Submit application (Job Seeker)
    Route::get('/myApplication', [ApplicationController::class, 'myApplications']); // Get logged-in user's applications
    Route::get('/job/{job_id}', [ApplicationController::class, 'getByJob']); // (Optional) Employer/Admin view
    Route::put('/{id}/status', [ApplicationController::class, 'updateStatus']) // Update status (Admin/Employer)
        ->middleware('role:admin,employer');
        
});