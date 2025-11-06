<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Category;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $fiction     = Category::where('name', 'Fiction')->first();
        $education   = Category::where('name', 'Education')->first();
        $nonfiction  = Category::where('name', 'Non-fiction')->first();
        $science     = Category::where('name', 'Science')->first();
        $technology  = Category::where('name', 'Technology')->first();
        $history     = Category::where('name', 'History')->first();
        $biography   = Category::where('name', 'Biography')->first();
        $fantasy     = Category::where('name', 'Fantasy')->first();
        $mystery     = Category::where('name', 'Mystery')->first();
        $romance     = Category::where('name', 'Romance')->first();

        $books = [
            [
                'title'       => '1984',
                'author'      => 'George Orwell',
                'description' => 'In a dystopian future where Big Brother watches every move, Winston Smith struggles to reclaim his humanity and truth in a world built on surveillance and propaganda. Orwell’s classic novel is a chilling exploration of totalitarianism and individual freedom, and remains profoundly relevant today.',
                'price'       => 75000,
                'stock'       => 12,
                'category_id' => $fiction->id ?? null,
                'image'       => 'books/1984-george-orwell.jpg',
            ],
            [
                'title'       => 'The Great Gatsby',
                'author'      => 'F. Scott Fitzgerald',
                'description' => 'Set in the high-life of 1920s Long Island, this novel follows Jay Gatsby’s obsessive love for Daisy Buchanan, his glittering parties and tragic fall. A timeless critique of the American Dream, wealth and desire.',
                'price'       => 68000,
                'stock'       => 8,
                'category_id' => $fiction->id ?? null,
                'image'       => 'books/the-great-gatsby.jpg',
            ],
            [
                'title'       => 'Sapiens: A Brief History of Humankind',
                'author'      => 'Yuval Noah Harari',
                'description' => 'From archaic hunter-gatherers to the modern digital age, Harari invites us to explore how Homo sapiens came to dominate the planet, how we built societies, economies and belief systems — and how we might evolve from here.',
                'price'       => 95000,
                'stock'       => 7,
                'category_id' => $nonfiction->id ?? null,
                'image'       => 'books/sapiens-yuval-noah-harari.jpg',
            ],
            [
                'title'       => 'Educated: A Memoir',
                'author'      => 'Tara Westover',
                'description' => 'Raised in a strict and isolated household in rural Idaho with no formal schooling, Tara Westover eventually escapes and goes on to earn a Ph.D. This powerful memoir reflects on education, family loyalty, and the transformative power of knowledge.',
                'price'       => 88000,
                'stock'       => 5,
                'category_id' => $education->id ?? null,
                'image'       => 'books/educated-tara-westover.jpg',
            ],
            [
                'title'       => 'Astrophysics for People in a Hurry',
                'author'      => 'Neil deGrasse Tyson',
                'description' => 'In succinct and engaging chapters, Tyson demystifies the cosmos — black holes, quantum mechanics, the big bang — in a way that invites busy minds to marvel at the universe without needing a physics degree.',
                'price'       => 99000,
                'stock'       => 9,
                'category_id' => $science->id ?? null,
                'image'       => 'books/astrophysics-for-people-in-a-hurry.jpg',
            ],
            [
                'title'       => 'The Innovators: How a Group of Hackers, Geniuses, and Geeks Created the Digital Revolution',
                'author'      => 'Walter Isaacson',
                'description' => 'Isaacson traces the trajectory of the digital age through the stories of key figures — Ada Lovelace, Alan Turing, Bill Gates, Steve Jobs — and shows how collaboration and creativity drove the innovations we now take for granted.',
                'price'       => 105000,
                'stock'       => 6,
                'category_id' => $technology->id ?? null,
                'image'       => 'books/the-innovators-walter-isaacson.jpg',
            ],
            [
                'title'       => 'The Wright Brothers',
                'author'      => 'David McCullough',
                'description' => 'A biography of Orville and Wilbur Wright, pioneers of aviation whose persistence, vision and sibling partnership changed the world forever by lifting human flight off the ground. McCullough captures the spirit of innovation and the human costs and triumphs behind it.',
                'price'       => 92000,
                'stock'       => 4,
                'category_id' => $biography->id ?? null,
                'image'       => 'books/the-wright-brothers-david-mccullough.jpg',
            ],
            [
                'title'       => 'The Name of the Wind',
                'author'      => 'Patrick Rothfuss',
                'description' => 'Kvothe, a magically-gifted performer and adventurer, recounts his journey from orphan to legendary figure in a world shaped by power, music and myth. An engrossing fantasy that blends intimate character work with epic scale.',
                'price'       => 82000,
                'stock'       => 10,
                'category_id' => $fantasy->id ?? null,
                'image'       => 'books/the-name-of-the-wind.jpg',
            ],
            [
                'title'       => 'Gone Girl',
                'author'      => 'Gillian Flynn',
                'description' => 'When Amy Dunne disappears on her fifth wedding anniversary, the truth behind her disappearance is darker, stranger and more twisted than anyone anticipated. A psychological thriller about marriage, media, and deception.',
                'price'       => 87000,
                'stock'       => 7,
                'category_id' => $mystery->id ?? null,
                'image'       => 'books/gone-girl-gillian-flynn.jpg',
            ],
            [
                'title'       => 'Pride and Prejudice',
                'author'      => 'Jane Austen',
                'description' => 'The spirited Elizabeth Bennet navigates British society, family expectations and the tumult of romance in this enduring love story that also offers sharp social commentary on class, gender and propriety in early 19th-century England.',
                'price'       => 78000,
                'stock'       => 11,
                'category_id' => $romance->id ?? null,
                'image'       => 'books/pride-and-prejudice-jane-austen.jpg',
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
