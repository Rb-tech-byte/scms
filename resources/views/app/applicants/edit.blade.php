@extends('layouts.app')
@section('title', 'Student List')
@section('header')
{!! $generatePageHeader('Student List', 'applicants.index', 'user', 'Manage Applicants', 'users', 'applicants.create', 'user-plus', 'Add New Applicant') !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <a href="{{ route('applicants.index') }}" class="mr-4"><i class="icon ion-md-arrow-back"></i></a>
                    @lang('crud.applicants.edit_title')
                </h4>

                <x-form method="PUT" action="{{ route('applicants.update', $applicant) }}" has-files class="mt-4">
                    @include('app.applicants.edit-form')

                    <div class="mt-4">
                        <a href="{{ route('applicants.index') }}" class="btn btn-light">
                            <i class="icon ion-md-return-left text-primary"></i>
                            @lang('crud.common.back')
                        </a>

                        <a href="{{ route('applicants.create') }}" class="btn btn-light">
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
