@extends('layouts.app')
@section('title', 'Show Branch')
@section('header')
    {!! $generatePageHeader(
        'Show Branch',
        'branches.index',
        'git-merge',
        'Manage branches',
        'git-merge',
        'branches.create',
        'plus',
        'add new branch',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="mt-4">
                    <div class="mb-4">
                        <h5>@lang('crud.branches.inputs.name')</h5>
                        <span>{{ $branch->name ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <h5>@lang('crud.branches.inputs.location')</h5>
                        <span>{{ $branch->location ?? '-' }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('branches.index') }}" class="btn btn-light">
                        <i class="icon ion-md-return-left"></i>
                        @lang('crud.common.back')
                    </a>

                    @check('Add branches')
                        <a href="{{ route('branches.create') }}" class="btn btn-light">
                            <i class="icon ion-md-add"></i> @lang('crud.common.create')
                        </a>
                    @endcheck
                </div>
            </div>
        </div>
    </div>
@endsection
