@extends('layouts.app')
<link href="{{ asset('assets/css/permission.css') }}" rel="stylesheet" />
@section('title', 'Manage permission')
@section('header')
    {!! $generatePageHeader(
        'manage permissions',
        'permission.manage',
        'lock',
        'Manage permissions',
        'lock',
        'permissions.create',
        'plus',
        'add permission',
    ) !!}
@endsection
@section('content')
    <div class="row">
        @php
            $categories =[];
            foreach ($permissions as $key) {
                $categories[$key->category]=$key->category;
            }
            $selected = [];
            foreach ($user_permissions as $perms) {
                $selected[$perms->permission_id]=[$perms];
            }
        @endphp
        <div class="card">
            <div class="card-body">
                <div class="mt-4 mb-4">
                    <select name="role" id="role_id" class="select2 form-control">
                        <option value="0">Select Role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ $role_id == $role->id ?'selected':'' }}> {{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="accordion" id="accordionExample">
                    @foreach ($categories as $category)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $category }}" aria-expanded="true" aria-controls="collapseOne">
                            {{ strtoupper($category) }}
                          </button>
                        </h2>
                        <div id="{{ $category }}" class="accordion-collapse collapse {{ $loop->iteration == 1 ?'show':'' }}" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                          <div class="accordion-body">
                            <ul type='none'>
                            @foreach ($permissions as $permission )
                                @if ($permission->category == $category)
                                <li><input type="checkbox" class="role" name="permission_id" {{ isset($selected[$permission->id])?'checked':'' }} id="{{ $permission->id }}"> <label for="{{ $permission->id }}">{{ ucfirst($permission->name) }}</label></li>
                                @endif
                            @endforeach
                        </ul>
                          </div>
                        </div>
                      </div>
                        
                    @endforeach
                    
                  </div>
                  
            </div>

        </div>
    </div>
@endsection
