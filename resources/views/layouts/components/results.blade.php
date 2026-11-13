@foreach ($results as $result)
    <ul type="none">
        @if ($result instanceof App\Models\Applicant)
            <li> <a href="{{ route('applicants.show', $result->id) }}">
                    <img src="{{ asset('mini.png') }}" height="20" />
                    {{ $result->full_name }} : Applicant

                </a>
            </li>
        @elseif ($result instanceof App\Models\Tutor)
            <li> <a href="{{ route('tutors.show', $result->id) }}">
                    @php
                        $tutorAvatar = profileImageUrl(optional($result->user)->image);
                    @endphp
                    @if ($tutorAvatar)
                        <img src="{{ $tutorAvatar }}" class="rounded-circle" alt="profile-image" height="20">
                    @else
                        <img class="rounded-circle" alt="profile-image" src="{{ asset('mini.png') }}" height="20" />
                    @endif

                    {{ $result->name }} : Tutor

                </a>
            </li>
        @elseif ($result instanceof App\Models\Course)
            <li> <a href="{{ route('courses.show', $result->id) }}">
                    <img src="{{ asset('mini.png') }}" height="20" />

                    {{ $result->name }} : Course

                </a>
            </li>
        @elseif ($result instanceof App\Models\Venue)
            <li> <a href="{{ route('venues.show', $result->id) }}">
                    <img src="{{ asset('mini.png') }}" height="20" />

                    {{ $result->name }} : Venue

                </a>
            </li>
        @elseif ($result instanceof App\Models\Branch)
            <li> <a href="{{ route('branches.show', $result->id) }}">
                    <img src="{{ asset('mini.png') }}" height="20" />

                    {{ $result->name }} : Branch

                </a>
            </li>
        @elseif ($result instanceof App\Models\Payment)
            <li> <a href="{{ route('payments.show', $result->id) }}">
                    <img src="{{ asset('mini.png') }}" height="20" />

                    {{ $result->receipt_number }} - {{ $result->applicant->full_name }}: Payment
                </a>
            </li>
        @endif
    </ul>
@endforeach
