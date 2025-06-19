<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
public function createCompany(Request $request)
{
    $validator = Validator::make($request->all(), [
        'company_name' => 'required|string|max:255|unique:companies,company_name',
        'company_details' => 'nullable|string',
        'company_logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048', // max 2MB
        'street' => 'required|string|max:255',
        'city' => 'required|string|max:100',
        'region' => 'required|string|max:100',
        'zip_code' => 'required|string|max:20',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }

    // Handle the image upload
    $logoPath = null;
    if ($request->hasFile('company_logo')) {
        $logoPath = $request->file('company_logo')->store('company_logos', 'public');
    }

    $company = Company::create([
        'user_id' => Auth::id(),
        'company_name' => $request->company_name,
        'company_details' => $request->company_details,
        'company_logo' => $logoPath, // store path
        'street' => $request->street,
        'city' => $request->city,
        'region' => $request->region,
        'zip_code' => $request->zip_code,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Company created successfully',
        'company' => $company,
    ], 201);
}

public function getCompanyById($id)
{
    $userId = Auth::id();

    $company = Company::with([
        'jobs.applications.user'  // Nested eager loading
    ])
    ->where('company_id', $id)
    ->where('user_id', $userId)
    ->first();

    if (!$company) {
        return response()->json([
            'success' => false,
            'message' => 'Company not found or access denied.',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'company' => $company,
        'jobs' => $company->jobs, // Includes applications and users
    ], 200);
}
public function deleteCompany($id)
{
    $userId = Auth::id();

    $company = Company::where('company_id', $id)
        ->where('user_id', $userId)
        ->first();

    if (!$company) {
        return response()->json([
            'success' => false,
            'message' => 'Company not found or access denied.',
        ], 404);
    }

    if ($company->company_logo) {
        \Storage::disk('public')->delete($company->company_logo);
    }

    $company->delete();

    return response()->json([
        'success' => true,
        'message' => 'Company deleted successfully.',
    ], 200);
}





}
