@extends('layouts.app')
@section('title', 'Open University Branches')
@section('header')
    {!! $generatePageHeader(
        'Branches',
        'branches.index',
        'git-merge',
        'Manage branches',
        'git-merge',
        'branches.create',
        'plus',
        'Add new branch',
    ) !!}
@endsection
@section('content')
    <div class="row">

        <div class="card">
            <div class="card-body">

                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th class="text-left">
                                @lang('crud.branches.inputs.name')
                            </th>
                            <th class="text-left">
                                @lang('crud.branches.inputs.location')
                            </th>
                            <th class="text-center">
                                @lang('crud.common.actions')
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branches as $branch)
                            <tr>
                                <td>{{ $branch->name ?? '-' }}</td>
                                <td>{{ $branch->location ?? '-' }}</td>
                                <td class="text-center" style="width: 134px;">
                                    <div role="group" aria-label="Row Actions" class="btn-group">
                                        @check('Update branches')
                                            <a href="{{ route('branches.edit', $branch) }}">
                                                <button type="button" class="btn btn-outline-primary">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </a>
                                            @endcheck
                                            @check('View branches')
                                            <a href="{{ route('branches.show', $branch) }}">
                                                <button type="button" class="btn btn-outline-info mx-1">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </a>
                                            @endcheck
                                            @check('Delete branches')
                                            
                                                <button type="submit" class="btn btn-outline-danger deletebtn" link="{{ route('branches.destroy', $branch) }}">
                                                    <i class="fa-solid fa-trash-alt"></i>
                                                </button>
                                        @endcheck
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    @lang('crud.common.no_items_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>
@endsection
