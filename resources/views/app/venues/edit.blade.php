@extends('layouts.app')
@section('title', 'Edit venue')
@section('header')
    {!! $generatePageHeader(
        'Edit venue',
        'venues.index',
        'home',
        'Manage venues',
        'home',
        'venues.create',
        'plus',
        'Edit venue',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <x-form method="PUT" action="{{ route('venues.update', $venue) }}" class="mt-4">
                    @include('app.venues.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('venues.index') }}" class="btn btn-light">
                            <i class="icon ion-md-return-left text-primary"></i>
                            @lang('crud.common.back')
                        </a>

                        <a href="{{ route('venues.create') }}" class="btn btn-light">
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
