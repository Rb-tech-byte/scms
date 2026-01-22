@extends('layouts.app')
@section('title', 'View permission')
@section('header')
    {!! $generatePageHeader(
        'View permission',
        'permissions.index',
        'lock',
        'Manage permissions',
        'lock',
        'permissions.create',
        'plus',
        'View permission',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="mt-4">
                    <div class="mb-4">
                        <h5>@lang('crud.permissions.inputs.name')</h5>
                        <span>{{ $permission->name ?? '-' }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('permissions.index') }}" class="btn btn-light">
                        <i class="icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @check('Add permissions')
                        <a href="{{ route('permissions.create') }}" class="btn btn-light">
                            <i class="icon ion-md-add"></i> @lang('crud.common.create')
                        </a>
                    @endcheck
                </div>
            </div>
        </div>
    </div>
@endsection
