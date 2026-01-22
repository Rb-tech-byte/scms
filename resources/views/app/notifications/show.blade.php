@extends('layouts.app')
@section('title', 'View notification')
@section('header')
    {!! $generatePageHeader(
        'View notification',
        'notifications.index',
        'mail',
        'Manage notifications',
        'mail',
        'notifications.create',
        'plus',
        'View notification',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <a href="{{ route('notifications.index') }}" class="mr-4"><i class="icon ion-md-arrow-back"></i></a>
                    @lang('crud.notifications.show_title')
                </h4>

                <div class="mt-4">
                    <div class="mb-4">
                        <h5>@lang('crud.notifications.inputs.applicant_id')</h5>
                        <span>{{ $notification->applicant_id ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.notifications.inputs.user_id')</h5>
                        <span>{{ $notification->user_id ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.notifications.inputs.message')</h5>
                        <span>{{ $notification->message ?? '-' }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('notifications.index') }}" class="btn btn-light">
                        <i class="icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @check('Add notifications')
                        <a href="{{ route('notifications.create') }}" class="btn btn-light">
                            <i class="icon ion-md-add"></i> @lang('crud.common.create')
                        </a>
                    @endcheck
                </div>
            </div>
        </div>
    </div>
@endsection
