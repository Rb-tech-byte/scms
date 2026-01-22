@extends('layouts.app')
@section('title', 'Add payment')
@section('header')
    {!! $generatePageHeader(
        'Add payments',
        'payments.index',
        'dollar-sign',
        'Manage payments',
        'dollar-sign',
        'payments.create',
        'plus',
        'Add New payment',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <x-form method="POST" action="{{ route('payments.store') }}" class="mt-4">
                    <div id="complete_status" class="alert alert-success alert-dismissible fade show" style="display:none">
                        
                        <small>Applicant has already paid Full Amount to this Course</small>
                        

                    </div>
                    @include('app.payments.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('payments.index') }}" class="btn btn-light">
                            <i class="icon ion-md-return-left text-primary"></i>
                            @lang('crud.common.back')
                        </a>

                        <button type="submit" class="btn btn-primary float-right" id="add_payment">
                            <i class="icon ion-md-save"></i>
                            Add Payment
                        </button>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
@endsection
