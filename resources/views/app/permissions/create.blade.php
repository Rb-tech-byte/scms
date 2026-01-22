@extends('layouts.app')
@section('title', 'Add permission')
@section('header')
    {!! $generatePageHeader(
        'Add permission',
        'permissions.index',
        'lock',
        'Manage permissions',
        'lock',
        'permissions.create',
        'plus',
        'Add New permission',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <x-form method="POST" action="{{ route('permissions.store') }}" class="mt-4">
                    @include('app.permissions.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('permissions.index') }}" class="btn btn-light">
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
