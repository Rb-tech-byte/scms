@extends('layouts.app')
@section('title', 'Add Country')
@section('header')
    {!! $generatePageHeader(
        'Add Country',
        'countries.index',
        'flag',
        'Manage countries',
        'flag',
        'countries.create',
        'plus',
        'Add Country',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <x-form method="POST" action="{{ route('countries.store') }}" class="mt-4">
                    @include('app.countries.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('countries.index') }}" class="btn btn-light">
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
