@extends('layouts.app')
@section('title', 'Notification Templates')
@section('content')
<div class="container-xl px-4 mt-4">
    <h1 class="h3 mb-3">Automated Email & SMS Notification Templates</h1>
    <p class="text-muted small">Supported placeholders: <code>{student_name}</code>, <code>{course_title}</code>, <code>{invoice_number}</code>, <code>{control_number}</code>, <code>{balance}</code>, <code>{due_date}</code>, <code>{tutor_name}</code>, <code>{branch_name}</code>, <code>{ready_count}</code>.</p>

    @forelse($templates as $template)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{{ $template->name }}</strong>
                <span class="badge bg-{{ $template->is_active ? 'success' : 'secondary' }}">{{ $template->is_active ? 'Active' : 'Disabled' }}</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('notifications.templates.update', $template) }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Channel</label>
                            <select name="channel" class="form-select">
                                <option value="email" {{ $template->channel === 'email' ? 'selected' : '' }}>Email</option>
                                <option value="sms" {{ $template->channel === 'sms' ? 'selected' : '' }}>SMS</option>
                                <option value="both" {{ $template->channel === 'both' ? 'selected' : '' }}>Both</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Subject</label>
                            <input type="text" name="subject" class="form-control" value="{{ $template->subject }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="tpl-{{ $template->id }}" {{ $template->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="tpl-{{ $template->id }}">Enabled</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Message Body</label>
                            <textarea name="body" rows="3" class="form-control" required>{{ $template->body }}</textarea>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary btn-sm">Save Template</button>
                    </div>
                </form>
            </div>
        </div>
    @empty
        <div class="alert alert-warning">No notification templates found.</div>
    @endforelse
</div>
@endsection
