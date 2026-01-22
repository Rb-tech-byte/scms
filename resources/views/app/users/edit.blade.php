@extends('layouts.app')
@section('title', 'Edit user')
@section('header')
    {!! $generatePageHeader(
        'Edit user',
        'users.index',
        'user',
        'Manage users',
        'users',
        'users.create',
        'user-plus',
        'Add New user',
    ) !!}
@endsection
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">

                <x-form method="PUT" action="{{ route('users.update', $user) }}" has-files class="mt-4">
                    @include('app.users.form-inputs')

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary float-right">
                            <i class="icon ion-md-save"></i>
                            @lang('crud.common.update')
                        </button>
                    </div>
                </x-form>
            </div>
            @check('Update permissions')
            <div class="card-body">
                <div class="col-sm-12">
                    <div class="container">
                        <h1 class="text-dark">permissions</h1>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="row">
                        <form method="POST" action="{{ route('user.updatePermissions', $user->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="col-sm-3">
                                        <div class="p-1">
                                            <div class="form-check">
                                                <input type="checkbox"  id="check-all" class="form-check-input">
                                                <label for="check-all" class="form-check-label text-danger">Check all</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                @foreach ($permissions->groupBy('category')->sortBy(function ($permissionsInCategory, $category) {
                                    return $category;
                                })->sortByDesc(function ($permissionsInCategory, $category) {
                                    return count($permissionsInCategory);
                                }) as $category => $permissionsInCategory)
                                    <div class="col-sm-3">
                                        <h5 class="text-primary mt-2"><span>{{ $loop->iteration. '.' }}</span>
                                            {{ ucfirst($category) }} Permissions ({{ count($permissionsInCategory) }})</h5>

                                        @foreach ($permissionsInCategory as $permission)
                                            <div class="p-1">
                                                    <div class="form-check">
                                                        <input type="checkbox" name="permissions[]" id="permission-{{ $permission->id }}" class="form-check-input permission-checkbox" value="{{ $permission->id }}" {{ $user->permissions->contains($permission->id)? 'checked' : '' }}>
                                                        <label for="permission-{{ $permission->id }}" class="form-check-label">{{ ucfirst(str_replace('-', ' ', $permission->name)) }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>

                                <div class="submit my-3">
                                    <button type="submit" class="btn btn-primary">Update Permissions</button>
                                </div>
                            </form>
                        {{-- end demo  --}}


                    </div>
                </div>
            </div>
            @endcheck
        </div>
    </div>
@endsection

@push('js')
<script>
    document.getElementById('check-all').addEventListener('click', function() {
        var checkboxes = document.querySelectorAll('.permission-checkbox');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = this.checked;
        }
    });
</script>
@endpush
