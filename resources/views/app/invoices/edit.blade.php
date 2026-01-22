@extends('layouts.app')
@section('title', 'Edit invoice')
@section('header')
    {!! $generatePageHeader(
        'Edit invoices',
        'invoices.index',
        'file-text',
        'Manage invoicess',
        'file-text',
        'invoices.create',
        'plus',
        'Edit New invoice',
    ) !!}
@endsection
@section('content')
<div class="row">
    <div class="card">
        <div class="card-body">

            <x-form
                method="PUT"
                action="{{ route('invoices.update', $invoice) }}"
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

                    <a
                        href="{{ route('invoices.create') }}"
                        class="btn btn-light"
                    >
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
