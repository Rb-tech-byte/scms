@extends('layouts.app')
@section('title', 'read messages')
@section('header')
    <header class="page-header page-header-dark open-blue-mode mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="life-buoy"></i></div>
                            Inbox
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>
@endsection

@section('content')
    <div class="container-xl px-4">
        @php
            $items = isset($messages) ? $messages : (isset($notifications) ? $notifications : collect());
        @endphp
        @forelse($items as $item)
        @php
            $isMessage = isset($item->subject);
            $title = $item->subject ?? 'Notification';
            $body = $item->body ?? ($item->message ?? '-');
            $created = optional($item->created_at)->diffForHumans();
            $href = $isMessage ? route('message.show', sst($item->id)) : (route('notifications.show', $item->id));
        @endphp
        <a class="card card-icon lift lift-sm mb-4" href="{{$href}}">
            <div class="row g-0">
                <div class="col-auto card-icon-aside bg-primary">
                    <img src="{{asset('assets/assets/img/favicon.png')}}" alt="..." class="img-fluid rounded-circle" style="max-width:80px;" />
                </div>
                <div class="col">
                    <div class="card-body py-4">
                        <h5 class="card-title text-primary mb-2">{{$title}}</h5>
                        <p class="card-text mb-1">{{$body}}.</p>
                        <div class="small text-muted">From: {{ 'SCMS OUT' }} |  {{$created}}</div>
                    </div>
                </div>
            </div>
        </a>
        @empty 
        <small>No messages yet</small>
        @endforelse
    </div>

@endsection
