@extends('layouts.app')
@section('title', 'Show venue')
@section('header')
    {!! $generatePageHeader(
        'Show venue',
        'venues.index',
        'home',
        'Manage venues',
        'home',
        'venues.create',
        'plus',
        'Show venue',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <div class="mt-4">
                    <div class="mb-4">
                        <h5>@lang('crud.venues.inputs.name')</h5>
                        <span>{{ $venue->name ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>Branch</h5>
                        <span>{{ optional($venue->branch)->name ?? '-' }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('venues.index') }}" class="btn btn-light">
                        <i class="icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @check('Add venues')
                        <a href="{{ route('venues.create') }}" class="btn btn-light">
                            <i class="icon ion-md-add"></i> @lang('crud.common.create')
                        </a>
                    @endcheck
                </div>
            </div>
        </div>
    </div>
@endsection
