@extends('layouts.app')
@section('title', 'Add user')
@section('header')
    {!! $generatePageHeader(
        'Add user',
        'users.index',
        'user',
        'Manage users',
        'users',
        'users.create',
        'user-plus',
        'Add New user',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <x-form method="POST" action="{{ route('users.store') }}" has-files class="mt-4">
                    @include('app.users.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('users.index') }}" class="btn btn-light">
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
