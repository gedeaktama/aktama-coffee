<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->latest()->paginate(10);
        return view('master.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
        ]);

        Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(4),
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Data kategori berhasil diperbarui.');
    }

    public function toggleStatus(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('categories.index')->with('success', "Kategori {$category->name} berhasil {$status}.");
    }
}
