<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Category;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $fiction = Category::where('name', 'Fiction')->first();
        $education = Category::where('name', 'Education')->first();
        $nonfiction = Category::where('name', 'Non-fiction')->first();

        $books = [
            [
                'title' => 'Whispers of the Wind',
                'author' => 'Lina Marquez',
                'description' => 'A touching story about love and destiny.',
                'price' => 85000,
                'stock' => 10,
                'category_id' => $fiction->id ?? null,
                'image' => 'books/whispers-of-the-wind.jpg',
            ],
            [
                'title' => 'Deep Learning Basics',
                'author' => 'Andrew Ng',
                'description' => 'An introduction to deep learning with practical examples.',
                'price' => 125000,
                'stock' => 5,
                'category_id' => $education->id ?? null,
                'image' => 'books/deep-learning-basics.jpg',
            ],
            [
                'title' => 'Mindful Living',
                'author' => 'Hiro Tanaka',
                'description' => 'A guide to self-awareness and mental well-being.',
                'price' => 99000,
                'stock' => 8,
                'category_id' => $nonfiction->id ?? null,
                'image' => 'books/mindful-living.jpg',
            ],
            [
                'title' => 'The Art of Coding',
                'author' => 'Sarah Klein',
                'description' => 'Discover the philosophy behind elegant programming.',
                'price' => 110000,
                'stock' => 6,
                'category_id' => $education->id ?? null,
                'image' => 'books/art-of-coding.jpg',
            ],
            [
                'title' => 'Journey Beyond Time',
                'author' => 'Amir Salim',
                'description' => 'A science-fiction adventure through parallel worlds.',
                'price' => 95000,
                'stock' => 12,
                'category_id' => $fiction->id ?? null,
                'image' => 'books/journey-beyond-time.jpg',
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
