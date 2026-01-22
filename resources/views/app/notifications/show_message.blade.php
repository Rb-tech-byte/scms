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
                        {{-- <div class="page-header-subtitle">What are you looking for? Our knowledge base is here to help.</div> --}}
                    </div>
                </div>
                {{-- <div class="page-header-search mt-4">
                <div class="input-group input-group-joined">
                    <input class="form-control" type="text" placeholder="Search..." aria-label="Search" autofocus />
                    <span class="input-group-text"><i data-feather="search"></i></span>
                </div>
            </div> --}}
            </div>
        </div>
    </header>
@endsection

@section('content')
    <div class="container-xl px-4">
        <a class="card card-icon lift lift-sm mb-4" href="{{route('message.show', sst($message->id))}}">
            <div class="row g-0">
                <div class="col-auto card-icon-aside bg-primary">
                    @if(!empty($user) && !empty($user->image))
                        <img src="{{asset(url(\Storage::url('profile/'.$staff->image)))}}" alt="..." class="img-fluid rounded-circle" style="max-width:80px;" />
                    @else
                        <img src="{{asset('assets/assets/img/favicon.png')}}" alt="..." class="img-fluid rounded-circle" style="max-width:80px;" />
                    @endif
                </div>
                <div class="col">
                    <div class="card-body py-4">
                        <h5 class="card-title text-primary mb-2">{{$message->subject ?? '-' }}</h5>
                        <p class="card-text mb-1">{{$message->body ?? '-'}}.</p>
                        <div class="small text-muted">From: {{$user->name ?? 'SCMS OUT'}} |  {{$message->created_at->diffForHumans()}}</div>
                    </div>
                </div>
            </div>
        </a>
    </div>

@endsection
