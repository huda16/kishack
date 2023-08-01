@extends('layouts.template')

@section('main')
    <div class="content-wrapper">
        <div class="content-body">
            <section class="p-3">
                <div class="row">
                    <div class="col-12">
                        <div class="card p-5">
                            <form action="/master-articles/{{ $story->slug }}" method="post" class="mb-5"
                                enctype="multipart/form-data">
                                @method('put')
                                @csrf
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        id="title" name="title" required autofocus
                                        value="{{ old('title', $story->title) }}">
                                    @error('title')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                        id="slug" name="slug" required value="{{ old('slug', $story->slug) }}">
                                    @error('slug')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-control select2" name="category_id">
                                        @foreach ($categories as $category)
                                            @if (old('category_id', $story->category_id) == $category->id)
                                                <option value="{{ $category->id }}" selected>{{ $category->name }}</option>
                                            @else
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="image" class="form-label">Article Image</label>
                                    <input type="hidden" name="oldImage" value="{{ $story->image }}">
                                    @if ($story->image)
                                        <img src="{{ asset('storage/' . $story->image) }}"
                                            class="img-preview img-fluid mb-3 col-sm-5 d-block">
                                    @else
                                        <img class="img-preview img-fluid mb-3 col-sm-5">
                                    @endif

                                    <input class="form-control @error('image') is-invalid @enderror" type="file"
                                        id="image" name="image" onchange="previewImage()">
                                    @error('image')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="body" class="form-label">Body</label>
                                    @error('body')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                    <textarea id="summernote" id="body" name="body">
                                        {{ old('body') }}
                                    </textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Edit Article</button>
                            </form>

                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@section('page_script')
    <script>
        $('.select2').select2();
        $('#summernote').summernote();

        const title = document.querySelector('#title');
        const slug = document.querySelector('#slug');

        title.addEventListener('change', function() {
            fetch('/master-articles/check-slug?title=' + title.value)
                .then(response => response.json())
                .then(data => slug.value = data.slug)
        });

        document.addEventListener('trix-file-accept', function(e) {
            e.preventDefault();
        })

        function previewImage() {
            const image = document.querySelector('#image');
            const imgPreview = document.querySelector('.img-preview');

            imgPreview.style.display = 'block';

            const ofReader = new FileReader();
            ofReader.readAsDataURL(image.files[0]);

            ofReader.onload = function(oFREvent) {
                imgPreview.src = oFREvent.target.result;
            }
        }
    </script>
@endsection
