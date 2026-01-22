@extends('layouts.app')
@section('title', 'Edit payment')
@section('header')
    {!! $generatePageHeader(
        'Edit payment',
        'payments.index',
        'dollar-sign',
        'Manage paymentss',
        'dollar-sign',
        'payments.create',
        'plus',
        'Edit New payment',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <x-form method="PUT" action="{{ route('payments.update', $payment) }}" class="mt-4">
                    @include('app.payments.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('payments.index') }}" class="btn btn-light">
                            <i class="icon ion-md-return-left text-primary"></i>
                            @lang('crud.common.back')
                        </a>

                        <a href="{{ route('payments.create') }}" class="btn btn-light">
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
