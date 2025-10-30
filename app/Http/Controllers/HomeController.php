<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class HomeController extends Controller
{
    public function index()
    {
        $books = Book::latest()->take(10)->get(); // contoh aja
        return view('home', compact('books'));
    }
}
