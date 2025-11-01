<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::orderBy('created_at', 'desc')->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.form', [
            'category' => new Category(),
            'method' => 'POST',
            'action' => route('admin.categories.store'),
        ]);
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category successfully added!');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.form', [
            'category' => $category,
            'method' => 'PUT',
            'action' => route('admin.categories.update', $category),
        ]);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category successfully updated!');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category successfully deleted!');
    }
}
