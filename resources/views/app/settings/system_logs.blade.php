@extends('layouts.app')
@section('title', 'System Logs')
@section('header')
    {!! $generatePageHeader('System Logs', 'system.logs.clear', 'save', 'Clear All Logs', 'trash-2') !!}
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="system_logs" class="table table-striped table-bordered table-hover datatable">
                            <thead>
                            <tr>
                                <th>Causer Name</th>
                                <th>Log event</th>
                                <th>Log Description</th>
                                <th>Subject Type</th>
                                <th>Subject ID</th>
                                <th>Causer Type</th>
                                <th>Properties</th>
                                <th>Created Date</th>
                                <th>Updated Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>{{ user_name($log->causer_id) }}</td>
                                    <td>{{ $log->event }}</td>
                                    <td>{{ $log->description }}</td>
                                    <td>{{ $log->subject_type ?? '-' }}</td>
                                    <td>{{ applicant_name($log->subject_id) }}</td>
                                    <td>{{ $log->causer_type }}</td>
                                    <td>{{ $log->properties }}</td>
                                    <td>{{ $log->created_at }}</td>
                                    <td>{{ $log->updated_at }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                           {{-- <tfoot>
                            <tr>
                                <td colspan="9">{!! $logs->render() !!}</td>
                            </tr>
                            </tfoot>--}}
                        </table>
                    </div> <!-- end table-responsive -->

                </div> <!-- end card body -->
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div>
@endsection
