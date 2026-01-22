@extends('layouts.app')
@section('title', 'Edit Staff')
@section('header')
    {!! $generatePageHeader(
        'All staffs',
        'users.index',
        'user',
        'Manage users',
        'users',
        'users.create',
        'user-plus',
        'Add Staff',
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
                                @lang('crud.users.inputs.name')
                            </th>
                            <th class="text-left">
                                @lang('crud.users.inputs.email')
                            </th>
                            <th class="text-left">
                                @lang('crud.users.inputs.phone')
                            </th>
                            <th>
                                Role
                            </th>
                            <th class="text-left">
                                @lang('crud.users.inputs.address')
                            </th>
                            <th class="text-left">
                                @lang('crud.users.inputs.image')
                            </th>
                            <th class="text-center">
                                @lang('crud.common.actions')
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)

                            <tr>
                                <td>{{ $user->name ?? '-' }}</td>
                                <td>{{ $user->email ?? '-' }}</td>
                                <td>{{ $user->phone ?? '-' }}</td>
                                <td>
                                  <span class="badge bg-green-soft text-green">{{ $user->role()->name ?? '-' }}</span>

                                </td>
                                <td>{{ $user->address ?? '-' }}</td>
                                <td>
                                    <x-partials.thumbnail
                                        src="{{ $user->image ? url(url(\Storage::url('profile/' . $user->image))) : '' }}" />
                                </td>
                                <td class="text-center" style="width: 134px;">
                                    <div role="group" aria-label="Row Actions" class="btn-group">

                                            <a href="{{ route('users.edit', $user) }}">
                                                <button type="button" class="btn btn-outline-primary">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </a>
                                            <a href="{{ route('users.show', $user) }}">
                                                <button type="button" class="btn btn-outline-info mx-1">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </a>

                                           
                                                <button type="submit" class="btn btn-outline-danger deletebtn" link="{{ route('users.destroy', $user) }}">
                                                    <i class="fa-solid fa-trash-alt"></i>
                                                </button>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
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
