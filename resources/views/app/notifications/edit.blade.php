@extends('layouts.app')
@section('title', 'Edit notification')
@section('header')
    {!! $generatePageHeader(
        'Edit notification',
        'notifications.index',
        'mail',
        'Manage notifications',
        'mail',
        'notifications.create',
        'plus',
        'Edit notification',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <a href="{{ route('notifications.index') }}" class="mr-4"><i class="icon ion-md-arrow-back"></i></a>
                    @lang('crud.notifications.edit_title')
                </h4>

                <x-form method="PUT" action="{{ route('notifications.update', $notification) }}" class="mt-4">
                    @include('app.notifications.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('notifications.index') }}" class="btn btn-light">
                            <i class="icon ion-md-return-left text-primary"></i>
                            @lang('crud.common.back')
                        </a>

                        <a href="{{ route('notifications.create') }}" class="btn btn-light">
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
