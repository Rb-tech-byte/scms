@extends('layouts.app')
@section('title', 'Open University Branches')
@section('header')
    {!! $generatePageHeader(
        'Open University Branches',
        'branches.index',
        'home',
        'Manage branches',
        'git-merge',
        'branches.create',
        'plus',
        'Add New branch',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <a href="{{ route('branches.index') }}" class="mr-4"><i class="icon ion-md-arrow-back"></i></a>
                    @lang('crud.branches.create_title')
                </h4>

                <x-form method="POST" action="{{ route('branches.store') }}" class="mt-4">
                    @include('app.branches.form-inputs')

                    <div class="mt-4">
                        <a href="{{ route('branches.index') }}" class="btn btn-light">
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
