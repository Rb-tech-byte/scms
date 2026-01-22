@extends('layouts.app')
@section('title', 'Countries')
@section('header')
    {!! $generatePageHeader(
        'Countries',
        'countries.index',
        'flag',
        'Manage countries',
        'flag',
        'countries.create',
        'plus',
        'Add Country',
    ) !!}
@endsection
@section('content')
    <div class="row">

        <div class="card">
            <div class="card-body">
                <div style="display: flex; justify-content: space-between;">
                    <h4 class="card-title">@lang('crud.countries.index_title')</h4>
                </div>

                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th class="text-left">
                                @lang('crud.countries.inputs.nicename')
                            </th>
                            <th class="text-left">
                                @lang('crud.countries.inputs.iso')
                            </th>
                            <th class="text-left">
                                @lang('crud.countries.inputs.iso3')
                            </th>
                            <th class="text-center">
                                @lang('crud.common.actions')
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($countries as $country)
                            <tr>
                                <td>{{ $country->nicename ?? '-' }}</td>
                                <td>{{ $country->iso ?? '-' }}</td>
                                <td>{{ $country->iso3 ?? '-' }}</td>
                                <td class="text-center" style="width: 134px;">
                                    <div role="group" aria-label="Row Actions" class="btn-group">
                                        @check('Update countries')
                                            <a href="{{ route('countries.edit', $country) }}">
                                                <button type="button" class="btn btn-outline-primary">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </a>
                                            @endcheck
                                            @check('View countries')
                                            <a href="{{ route('countries.show', $country) }}">
                                                <button type="button" class="btn btn-outline-info mx-1">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </a>
                                            @endcheck
                                            @check('Delete countries')
                                            
                                                <button type="submit" class="btn btn-outline-danger deletebtn" link="{{ route('countries.destroy', $country) }}">
                                                    <i class="fa-solid fa-trash-alt"></i>
                                                </button>
                                        @endcheck
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
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
