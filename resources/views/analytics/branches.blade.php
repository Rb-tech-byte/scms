@extends('layouts.app')
@section('title', 'Branch Performance Analytics')
@section('content')
<div class="container-xl px-4 mt-4">
    <h1 class="h3 mb-3">Regional Centre Performance & Revenue</h1>
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Centre Name</th>
                        <th>Region</th>
                        <th class="text-center">Venues</th>
                        <th class="text-center">Applicants</th>
                        <th class="text-end">Collected (TZS)</th>
                        <th class="text-end">Outstanding (TZS)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $b)
                        <tr>
                            <td class="fw-bold">{{ $b['name'] }}</td>
                            <td>{{ $b['region'] }}</td>
                            <td class="text-center">{{ $b['venues_count'] }}</td>
                            <td class="text-center">{{ $b['applicants_count'] }}</td>
                            <td class="text-end text-success">{{ number_format($b['collected'], 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($b['outstanding'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No branch metrics found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
