@extends('layouts.app')

@section('title', 'Page Expired')

@section('content')
    <div class="container py-5">
        <h1 class="text-danger">419 | Page Expired</h1>
        <p>Your session has expired. Please refresh the page and try again.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Go Home</a>
    </div>
@endsection

