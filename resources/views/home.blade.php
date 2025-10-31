@extends('layouts.app')

@section('content')
  <div class="min-h-screen">
    <div class="bg-white border shadow-lg rounded-sm sm:m-10">
      @include('layouts.navigation')
      
      @include('home.banner')
      @include('home.search')
      @include('home.catalog')
      @include('home.about')
      @include('home.order')
      @include('home.footer')
    </div>
  </div>
@endsection

{{-- Modals --}}
@include('components.modals.auth')