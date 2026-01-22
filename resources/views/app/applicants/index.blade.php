@extends('layouts.app')
@section('title', 'Student List')
@section('header')
    {!! $generatePageHeader(
        'Student List',
        'applicants.index',
        'user',
        'Manage Applicants',
        'users',
        'applicants.create',
        'user-plus',
        'Add New Applicant',
    ) !!}
@endsection

@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <table id="datatablesSimple" class="table table-hover">
                    <thead>
                        <tr>
                            <th class="text-left">Details</th>
                            <th class="text-left">Contacts</th>
                            <th class="text-left">@lang('crud.applicants.inputs.education_level')</th>
                            <th class="text-center">@lang('crud.common.actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applicants as $applicant)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        {{-- Avatar / Image --}}
                                        <div class="avatar me-2">
                                            @if (optional($applicant->user)->image)
                                                <img src="{{ asset('storage/profiles/' . $applicant->user->image) }}"
                                                    alt="{{ $applicant->full_name }}" class="rounded-circle"
                                                    style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px; font-weight: bold;">
                                                    {{ strtoupper(substr($applicant->full_name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Name & Tags --}}
                                        <div>
                                            <span class="fw-bold d-block">{{ $applicant->full_name ?? '-' }}</span>
                                            <small class="text-muted">
                                                Created: {{ $applicant->created_at ? $applicant->created_at->format('d M Y') : '-' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="mb-1">
                                            <i class="fa-solid fa-phone me-1 text-muted"></i>
                                            {{ $applicant->mobile ?? '-' }}
                                        </span>
                                        <span>
                                            <i class="fa-solid fa-envelope me-1 text-muted"></i>
                                            {{ $applicant->email ?? '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $eduClass = getEducationLevelClass($applicant->education_level);
                                        // Map the class to a nicer label if possible, or just capitalize
                                        $eduLabel = $eduClass ? ucwords(str_replace('-', ' ', $eduClass)) : '-';
                                    @endphp
                                    <span class="badge border border-primary text-primary">
                                        {{ $eduLabel }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group gap-1" role="group">
                                        @check('View applicants')
                                            <a href="{{ route('applicants.show', $applicant) }}" class="btn btn-sm btn-light text-primary"
                                                title="View Details">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        @endcheck

                                        @check('Update applicants')
                                            <a href="{{ route('applicants.edit', $applicant) }}" class="btn btn-sm btn-light text-success"
                                                title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                        @endcheck

                                        @check('Delete applicants')
                                            <button type="button" class="btn btn-sm btn-light text-danger deletebtn"
                                                link="{{ route('applicants.destroy', $applicant) }}" title="Delete">
                                                <i class="fa-solid fa-trash-alt"></i>
                                            </button>
                                        @endcheck
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fa-regular fa-folder-open fa-2x text-muted mb-2"></i>
                                        <span class="text-muted">@lang('crud.common.no_items_found')</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
