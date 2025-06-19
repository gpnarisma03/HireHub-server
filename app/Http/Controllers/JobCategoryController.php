<?php

namespace App\Http\Controllers;

use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:admin'])->except(['getAllCategories']);
    }

    // Add a new category
    public function addCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|unique:job_categories,category_name|max:255',
            'category_logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $logoPath = null;
        if ($request->hasFile('category_logo')) {
            $logoPath = $request->file('category_logo')->store('category_logos', 'public');
        }

        $category = JobCategory::create([
            'category_name' => $request->category_name,
            'category_logo' => $logoPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category added successfully',
            'category' => $category,
        ], 201);
    }

    // Update a category by ID
    public function updateCategory(Request $request, $id)
    {
        $category = JobCategory::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:255|unique:job_categories,category_name,' . $id . ',category_id',
            'category_logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $category->category_name = $request->category_name;

        // Optional: update logo if uploaded
        if ($request->hasFile('category_logo')) {
            $logoPath = $request->file('category_logo')->store('category_logos', 'public');
            $category->category_logo = $logoPath;
        }

        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'category' => $category,
        ]);
    }

    // Delete a category by ID
    public function deleteCategory($id)
    {
        $category = JobCategory::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ]);
    }

public function getAllCategories()
{
    $categories = JobCategory::withSum(['jobs as open_jobs_sum_job_vacancy' => function ($query) {
        $query->where('status', 'open');
    }], 'job_vacancy')->get();

    return response()->json([
        'success' => true,
        'categories' => $categories,
    ]);
}

}
