@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg text-center">
        <h1 class="text-5xl font-bold text-gray-900 dark:text-gray-100">
            {{ __("Welcome to Restaurant Management System!") }}
        </h1>
    </div>
</div>

@endsection