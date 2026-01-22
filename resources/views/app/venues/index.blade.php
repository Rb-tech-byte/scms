@extends('layouts.app')
@section('title', 'venue')
@section('header')
    {!! $generatePageHeader(
        'venue',
        'venues.index',
        'home',
        'Manage venues',
        'home',
        'venues.create',
        'plus',
        'venue',
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
                                @lang('crud.venues.inputs.name')
                            </th>
                            <th class="text-left">
                                @lang('crud.venues.inputs.branch_id')
                            </th>
                            <th class="text-center">
                                @lang('crud.common.actions')
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($venues as $venue)
                            <tr>
                                <td>{{ $venue->name ?? '-' }}</td>
                                <td>{{ optional($venue->branch)->name ?? '-' }}</td>
                                <td class="text-center" style="width: 134px;">
                                    <div role="group" aria-label="Row Actions" class="btn-group">
                                        @check('Update venues')
                                            <a href="{{ route('venues.edit', $venue) }}">
                                                <button type="button" class="btn btn-outline-primary">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </a>
                                            @endcheck
                                            @check('View venues')
                                            <a href="{{ route('venues.show', $venue) }}">
                                                <button type="button" class="btn btn-outline-info mx-1">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </a>
                                            @endcheck
                                            @check('Delete venues')
                                                <button type="submit" class="btn btn-outline-danger deletebtn" link="{{ route('venues.destroy', $venue) }}">
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
