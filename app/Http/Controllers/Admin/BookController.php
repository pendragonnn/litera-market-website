<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('category')->get();
        return view('admin.books.index', compact('books'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.books.form', [
            'book' => new Book(),
            'categories' => $categories,
            'method' => 'POST',
            'action' => route('admin.books.store'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Simpan data buku tanpa gambar dulu untuk dapat ID
        $book = Book::create(Arr::except($validated, ['image']));

        // Kalau admin upload gambar
        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $filename = Str::slug($book->title) . '_' . $book->id . '.' . $extension;

            // Path tujuan: public/books/
            $destination = public_path('books');

            // Pastikan folder 'public/books' ada
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            // Pindahkan file ke folder publik
            $request->file('image')->move($destination, $filename);

            // Simpan path relatif ke database
            $book->update(['image' => 'books/' . $filename]);
        }

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Book successfully added!');
    }

    public function edit(Book $book)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.books.form', [
            'book' => $book,
            'categories' => $categories,
            'method' => 'PUT',
            'action' => route('admin.books.update', $book),
        ]);
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Jika admin upload gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($book->image && file_exists(public_path($book->image))) {
                unlink(public_path($book->image));
            }

            // Buat nama file baru berdasarkan title dan ID buku
            $extension = $request->file('image')->getClientOriginalExtension();
            $filename = Str::slug($book->title) . '_' . $book->id . '.' . $extension;

            // Pastikan folder public/books ada
            $destination = public_path('books');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            // Pindahkan file baru ke folder public/books
            $request->file('image')->move($destination, $filename);

            // Simpan path relatif ke database
            $validated['image'] = 'books/' . $filename;
        }

        // Update data buku
        $book->update($validated);

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Book successfully updated!');
    }

    public function destroy(Book $book)
    {
        if ($book->image) {
            Storage::disk('public')->delete($book->image);
        }

        $book->delete();

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Book successfully deleted!');
    }
}
