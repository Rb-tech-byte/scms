<li class="nav-item dropdown no-caret d-none d-sm-block me-3 dropdown-notifications">
    <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownMessages" href="javascript:void(0);"
        role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="mail"></i></a>
    <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up"
        aria-labelledby="navbarDropdownMessages">
        <h6 class="dropdown-header dropdown-notifications-header">
            <i class="me-2" data-feather="mail"></i>
            Emails
        </h6>
        @php
            $messages = App\Models\Message::where('receiver_id', Auth::user()->id)->get();
        @endphp
        @forelse($messages as $message)
            <a class="dropdown-item dropdown-notifications-item" href="{{ route('message.show', sst($message->id)) }}">
                <img class="dropdown-notifications-item-img" src="{{ asset('assets/assets/img/favicon.png') }}" />
                <div class="dropdown-notifications-item-content">
                    <div class="dropdown-notifications-item-content-text">{{ $message->body }}</div>
                    <div class="dropdown-notifications-item-content-details">{{ $message->created_at->diffForHumans() }}
                    </div>
                </div>
            </a>
        @empty
            <a class="dropdown-item dropdown-notifications-item" href="#!">
                <div class="dropdown-notifications-item-content">
                    <div class="dropdown-notifications-item-content-text text-center">No Message</div>
                </div>
            </a>
        @endforelse
        <a class="dropdown-item dropdown-notifications-footer" href="{{route('message.show_all')}}">Read All notifications</a>
    </div>
</li>
