
@extends('layouts.app')
@section('title', 'Update Tutor')
@section('header')
    {!! $generatePageHeader(
        'Update Tutor',
        'tutors.index',
        'users',
        'Manage Tutor',
        'users',
        'tutors.create',
        'plus',
        'Add Tutor',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <x-form
                    method="PUT"
                    action="{{ route('tutors.update', $tutor) }}"
                    has-files
                    class="mt-4"
                >
                    @include('app.tutors.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('tutors.index') }}" class="btn btn-light">
                            <i class="icon ion-md-return-left text-primary"></i>
                            @lang('crud.common.back')
                        </a>

                        <a
                            href="{{ route('tutors.create') }}"
                            class="btn btn-light"
                        >
                            <i class="icon ion-md-add text-primary"></i>
                            @lang('crud.common.create')
                        </a>

                        <button type="submit" class="btn btn-primary float-right">
                            <i class="icon ion-md-save"></i>
                            @lang('crud.common.update')
                        </button>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
@endsection


