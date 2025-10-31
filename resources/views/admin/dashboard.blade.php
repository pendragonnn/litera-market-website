@extends('layouts.app')

@section('content')
<div class="text-center mt-8">
  <h1 class="text-2xl font-bold text-blue-600">Admin Dashboard</h1>
  <p class="text-gray-600 mt-2">Welcome, {{ Auth::user()->name }} 👋</p>
</div>
@endsection
