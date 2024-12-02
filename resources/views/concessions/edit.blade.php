@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content')
<!-- Registration 7 - Bootstrap Brain Component -->
<section class="bg-light p-3 p-md-4 p-xl-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-7 col-xl-6 col-xxl-5">
                <div class="card border border-light-subtle rounded-4">
                    <div class="card-body p-3 p-md-4 p-xl-5">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-5">
                                    <h2 class="h4 text-center">Edit Concession</h2>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('concession.update', $concessions->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row gy-3 overflow-hidden">
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            value="{{ old('name', $concessions->name) }}" placeholder="Name">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <input type="text" class="form-control" name="description" id="description"
                                            value="{{ old('description', $concessions->description) }}"
                                            placeholder="Description">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div>
                                        <label for="image" class="form-label">Image</label>
                                        <input class="form-control" id="image" name="image" type="file">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <label for="price" class="form-label">Price</label>
                                        <input type="text" class="form-control" name="price" id="price" value=""
                                            value="{{ old('price', number_format($concession->price ?? 0, 2)) }}"
                                            placeholder="Price">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-grid">
                                        <button class="btn bsb-btn-xl btn-primary" type="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection