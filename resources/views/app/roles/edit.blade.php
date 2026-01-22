@extends('layouts.app')
@section('title', 'Edit role')
@section('header')
    {!! $generatePageHeader(
        'Edit role',
        'roles.index',
        'key',
        'Manage roles',
        'key',
        'roles.create',
        'plus',
        'Edit role',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <a href="{{ route('roles.index') }}" class="mr-4"><i class="icon ion-md-arrow-back"></i></a>
                    Edit " <span class="badge bg-blue-soft text-blue">{{ $role->name }}</span> " Permissions
                </h4>

                <x-form method="PUT" action="{{ route('roles.update', $role) }}" class="mt-4">
                    @include('app.roles.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('roles.index') }}" class="btn btn-light">
                            <i class="icon ion-md-return-left text-primary"></i>
                            @lang('crud.common.back')
                        </a>

                        <a href="{{ route('roles.create') }}" class="btn btn-light">
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
