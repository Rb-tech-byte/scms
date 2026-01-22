@extends('layouts.app')
@section('title', 'Add notification')
@section('header')
    {!! $generatePageHeader(
        'Add notification',
        'notifications.index',
        'mail',
        'Manage notifications',
        'mail',
        'notifications.create',
        'plus',
        'Add notification',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <x-form method="POST" action="{{ route('notifications.store') }}" class="mt-4">
                    @include('app.notifications.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('notifications.index') }}" class="btn btn-light">
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
