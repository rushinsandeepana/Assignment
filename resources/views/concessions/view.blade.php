@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    <a href="{{ route('concession.add') }}">
        <button type="button" class="btn btn-primary p-0" style="font-size: 24px; font-weight: bold; width: 100px;">
            <span class="text-5xl font-bold p-0 m-0">+ &nbsp; New</span>
        </button>
    </a>
</h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 ">
                <h3 class="text-bold">View All Concessions</h3>
            </div>
        </div>
    </div>
</div>

<div class="album py-5 bg-light">
    <div class="container">

        <div class="row">
            @foreach ($concessions as $concession )
            <div class="col-md-4">
                <div class="card mb-4 box-shadow">
                    <img class="card-img-top img-fluid. max-width: 100%;"
                        style="height: 210px; object-fit: cover; width: 100%;"
                        src="{{ $concession->image ? asset('storage/' . $concession->image) : asset('assets/default-image.jpg') }}"
                        alt="Card image cap">
                    <div class="card-body">
                        <h5 class="text-bold">{{$concession->name}}</h5>
                        <p class="card-text">{{$concession->description}}.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <a href="{{ route('concession.edit', $concession->id) }}">
                                    <button type="button" class="btn btn-sm  btn-success mr-2">Edit</button>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger">Delete</button>
                            </div>
                            <span class="text-bold fs-3" style="font-size: 32px;">Rs.{{$concession->price}}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection