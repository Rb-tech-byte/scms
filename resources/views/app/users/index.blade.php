@extends('layouts.app')
@section('title', 'System Users')
@section('header')
    {!! $generatePageHeader(
        'System Users',
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
        <div class="card mb-4 w-100">
            <div class="card-body">
                <form method="GET" action="{{ route('users.index') }}" class="row g-2 mb-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Name, email or phone">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">User Type</label>
                        <select name="user_type" class="form-select form-select-sm">
                            <option value="all" {{ request('user_type', 'all') === 'all' ? 'selected' : '' }}>All Users</option>
                            <option value="staff" {{ request('user_type') === 'staff' ? 'selected' : '' }}>Staff Only</option>
                            <option value="student" {{ request('user_type') === 'student' ? 'selected' : '' }}>Students Only</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Role</label>
                        <select name="role_id" class="form-select form-select-sm">
                            <option value="">All Roles</option>
                            @foreach($roles ?? [] as $role)
                                <option value="{{ $role->id }}" {{ (string)request('role_id') === (string)$role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Centre / Branch</label>
                        <select name="branch_id" class="form-select form-select-sm">
                            <option value="">All Centres</option>
                            @foreach($branches ?? [] as $branch)
                                <option value="{{ $branch->id }}" {{ (string)request('branch_id') === (string)$branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Account Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked / Locked</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-grid">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-filter"></i> Filter</button>
                    </div>
                </form>

                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th class="text-left">@lang('crud.users.inputs.name')</th>
                            <th class="text-left">@lang('crud.users.inputs.email')</th>
                            <th class="text-left">@lang('crud.users.inputs.phone')</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-left">Centre</th>
                            <th class="text-center">@lang('crud.common.actions')</th>
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
                                <td>
                                    @if($user->is_blocked)
                                        <span class="badge bg-danger">Blocked</span>
                                    @elseif($user->isLockedOut())
                                        <span class="badge bg-warning text-dark">Temp Locked</span>
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif
                                </td>
                                <td>{{ $user->branch?->name ?? ($user->applicant_branch() ?: '-') }}</td>
                                <td class="text-center" style="width: 190px;">
                                    <div role="group" aria-label="Row Actions" class="btn-group">
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-info mx-1" title="View">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        @if(auth()->id() !== $user->id)
                                            <form method="POST" action="{{ route('users.toggle-block', $user) }}" class="d-inline">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm {{ $user->is_blocked ? 'btn-outline-success' : 'btn-outline-warning' }} me-1"
                                                    title="{{ $user->is_blocked ? 'Unblock User' : 'Block User' }}">
                                                    <i class="fa-solid {{ $user->is_blocked ? 'fa-unlock' : 'fa-ban' }}"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <button type="submit" class="btn btn-sm btn-outline-danger deletebtn" link="{{ route('users.destroy', $user) }}" title="Delete">
                                            <i class="fa-solid fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">@lang('crud.common.no_items_found')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
