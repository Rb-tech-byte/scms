@extends('layouts.app')
@section('title', 'Edit Countries')
@section('header')
    {!! $generatePageHeader(
        'Countries',
        'countries.index',
        'flag',
        'Manage countries',
        'flag',
        'countries.edit',
        'plus',
        'Edit Country',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <x-form method="PUT" action="{{ route('countries.update', $country) }}" class="mt-4">
                    @include('app.countries.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('countries.index') }}" class="btn btn-light">
                            <i class="icon ion-md-return-left text-primary"></i>
                            @lang('crud.common.back')
                        </a>

                        <a href="{{ route('countries.create') }}" class="btn btn-light">
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
