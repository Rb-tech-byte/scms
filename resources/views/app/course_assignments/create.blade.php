@extends('layouts.app')
@section('title', 'Course Assignment')
@section('header')
    {!! $generatePageHeader(
        'Course Assignment',
        'course-assignments.index',
        'home',
        'Manage course-assignments',
        'book-open',
        'course-assignments.create',
        'plus',
        'assign Tutor to Course',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <x-form method="POST" action="{{ route('course-assignments.store') }}" class="mt-4">
                    @include('app.course_assignments.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('course-assignments.index') }}" class="btn btn-light">
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
