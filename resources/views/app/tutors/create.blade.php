@extends('layouts.app')
@section('title', 'Add New Tutor')
@section('header')
    {!! $generatePageHeader(
        'Add New Tutor',
        'tutors.index',
        'users',
        'Manage Tutor',
        'users',
        'tutors.create',
        'plus',
        'Add New Tutor',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <x-form
                    method="POST"
                    action="{{ route('tutors.store') }}"
                    has-files
                    class="mt-4"
                >
                    @include('app.tutors.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('tutors.index') }}" class="btn btn-light">
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

