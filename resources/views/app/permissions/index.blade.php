@extends('layouts.app')
@section('title', 'permission')
@section('header')
    {!! $generatePageHeader(
        'permission',
        'permission.manage',
        'lock',
        'Manage permissions',
        'lock',
        'permissions.create',
        'plus',
        'permission',
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
                                @lang('crud.permissions.inputs.name')
                            </th>
                            <th class="text-center">
                                @lang('crud.common.actions')
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permissions as $permission)
                            <tr>
                                <td>{{ $permission->name ?? '-' }}</td>
                                <td class="text-center" style="width: 134px;">
                                    <div role="group" aria-label="Row Actions" class="btn-group">
                                        @check('Update permissions')
                                            <a href="{{ route('permissions.edit', $permission) }}">
                                                <button type="button" class="btn btn-outline-primary">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </a>
                                            @endcheck
                                            @check('View permissions')
                                            <a href="{{ route('permissions.show', $permission) }}">
                                                <button type="button" class="btn btn-outline-info mx-1">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </a>
                                            @endcheck
                                            @check('Delete permissions')
                                           
                                                <button type="submit" class="btn btn-outline-danger deletebtn" link="{{ route('permissions.destroy', $permission) }}">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                        @endcheck
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">
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
