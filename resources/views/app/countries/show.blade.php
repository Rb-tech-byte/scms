@extends('layouts.app')
@section('title', 'Show Countries')
@section('header')
    {!! $generatePageHeader(
        'Countries',
        'countries.index',
        'flag',
        'Manage countries',
        'flag',
        'countries.create',
        'plus',
        'View Country',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <a href="{{ route('countries.index') }}" class="mr-4"><i class="icon ion-md-arrow-back"></i></a>
                    @lang('crud.countries.show_title')
                </h4>

                <div class="mt-4">
                    <div class="mb-4">
                        <h5>@lang('crud.countries.inputs.nicename')</h5>
                        <span>{{ $country->nicename ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.countries.inputs.iso')</h5>
                        <span>{{ $country->iso ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.countries.inputs.iso3')</h5>
                        <span>{{ $country->iso3 ?? '-' }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('countries.index') }}" class="btn btn-light">
                        <i class="icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @check('Add countries')
                        <a href="{{ route('countries.create') }}" class="btn btn-light">
                            <i class="icon ion-md-add"></i> @lang('crud.common.create')
                        </a>
                    @endcheck
                </div>
            </div>
        </div>
    </div>
@endsection
