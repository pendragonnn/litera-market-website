@extends('layouts.app')

@section('content')
<div class="container mx-auto p-8">
    <h1 class="text-2xl font-bold mb-4">Welcome to LiteraMarket 📚</h1>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach ($books as $book)
            <div class="p-4 border rounded-lg bg-white shadow-sm hover:shadow-md transition">
                <img src="{{ $book->image }}" alt="{{ $book->title }}" class="rounded mb-2">
                <h2 class="font-semibold">{{ $book->title }}</h2>
                <p class="text-gray-500 text-sm">Rp {{ number_format($book->price, 0, ',', '.') }}</p>
            </div>
        @endforeach
    </div>
</div>
@endsection
