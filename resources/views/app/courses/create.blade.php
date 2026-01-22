@extends('layouts.app')
@section('title', 'Add Course')
@section('header')
    {!! $generatePageHeader(
        'Add New Course',
        'courses.index',
        'book-open',
        'Manage courses',
        'book-open',
        'courses.create',
        'plus',
        'Add New Course',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <x-form method="POST" action="{{ route('courses.store') }}" class="mt-4">
                    @include('app.courses.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('courses.index') }}" class="btn btn-light">
                            <i class="icon ion-md-return-left text-primary"></i>
                            @lang('crud.common.back')
                        </a>

                        <button type="submit" class="btn btn-primary float-right">
                            <i class="icon ion-md-save"></i>
                            @lang('crud.common.create')
                        </button>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
@endsection
