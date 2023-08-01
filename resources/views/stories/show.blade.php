@extends('layouts.template')

@section('main')
    <div class="content-wrapper">
        <div class="content-body">
            <section class="p-3">
                <div class="row">
                    <div class="col-12">
                        <div class="card p-5">
                            <h1 class="mb-3">{{ $story->title }}</h1>

                            <div class="">
                                <a href="/master-articles" class="btn btn-success">
                                    Back to
                                    all
                                    my
                                    posts</a>
                                <a href="/master-articles/{{ $story->slug }}/edit" class="btn btn-warning">
                                    Edit</a>
                            </div>

                            @if ($story->image)
                                <div style="max-height: 350px;overflow: hidden;">
                                    <img src="{{ asset('storage/' . $story->image) }}" alt="{{ $story->category->name }}"
                                        class="img-fluid mt-3">
                                </div>
                            @else
                                <img src="https://source.unsplash.com/1200x400?{{ $story->category->name }}"
                                    alt="{{ $story->category->name }}" class="img-fluid mt-3">
                            @endif
                            <article class="my-3 fs-5">
                                {!! $story->body !!}
                            </article>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
