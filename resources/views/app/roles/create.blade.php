@extends('layouts.app')
@section('title', 'Add role')
@section('header')
    {!! $generatePageHeader(
        'Add role',
        'roles.index',
        'key',
        'Manage roles',
        'key',
        'roles.create',
        'plus',
        'Add role',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <x-form method="POST" action="{{ route('roles.store') }}" class="mt-4">
                    @include('app.roles.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('roles.index') }}" class="btn btn-light">
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
