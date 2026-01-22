@extends('layouts.app')
@section('title', 'Edit permission')
@section('header')
    {!! $generatePageHeader(
        'Edit permission',
        'permissions.index',
        'lock',
        'Manage permissions',
        'lock',
        'permissions.create',
        'plus',
        'Edit permission',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <x-form method="PUT" action="{{ route('permissions.update', $permission) }}" class="mt-4">
                    @include('app.permissions.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('permissions.index') }}" class="btn btn-light">
                            <i class="icon ion-md-return-left text-primary"></i>
                            @lang('crud.common.back')
                        </a>

                        <a href="{{ route('permissions.create') }}" class="btn btn-light">
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
