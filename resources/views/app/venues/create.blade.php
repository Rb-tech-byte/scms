@extends('layouts.app')
@section('title', 'Add venue')
@section('header')
    {!! $generatePageHeader(
        'Add venue',
        'venues.index',
        'home',
        'Manage venues',
        'home',
        'venues.create',
        'plus',
        'Add venue',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <x-form method="POST" action="{{ route('venues.store') }}" class="mt-4">
                    @include('app.venues.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('venues.index') }}" class="btn btn-light">
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
