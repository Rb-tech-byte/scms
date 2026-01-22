@extends('layouts.app')
@section('title', 'Create invoice')
@section('header')
    {!! $generatePageHeader(
        'Create invoices',
        'invoices.index',
        'file-text',
        'Manage invoicess',
        'file-text',
        'invoices.create',
        'plus',
        'Create New invoice',
    ) !!}
@endsection
@section('content')
<div class="row">
    <div class="card">
        <div class="card-body">

            <x-form
                method="POST"
                action="{{ route('invoices.store') }}"
                class="mt-4"
            >
                @include('app.invoices.form-inputs')

                <div class="mt-4">
                    <a
                        href="{{ route('invoices.index') }}"
                        class="btn btn-light"
                    >
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
