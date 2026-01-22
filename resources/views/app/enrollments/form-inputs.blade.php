@php $editing = isset($enrollment) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12 col-lg-6">
        <x-inputs.select name="applicant_id" label="Applicant" required>
            @php $selected = old('applicant_id', ($editing ? $enrollment->applicant_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Applicant</option>
            @foreach ($applicants as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-6">
        <x-inputs.select name="course_id" label="Course Title" required>
            @php $selected = old('course_id', ($editing ? $enrollment->course_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Course</option>
            @foreach ($courses as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-4">
        <x-inputs.select name="session" label="Session">
            @php $selected = old('session', ($editing ? $enrollment->session : 'morning')) @endphp
            <option value="morning" {{ $selected == 'morning' ? 'selected' : '' }}>Morning</option>
            <option value="afternoon" {{ $selected == 'afternoon' ? 'selected' : '' }}>afternoon</option>
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-4">
        <x-inputs.select name="venue_id" label="Venue" required>
            @php $selected = old('venue_id', ($editing ? $enrollment->venue_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Venue</option>
            @foreach ($venues as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-4">
        <x-inputs.date name="start_date" label="Start Date"
            value="{{ old('start_date', $editing ? optional($enrollment->start_date)->format('Y-m-d') : '') }}"
            max="255" required></x-inputs.date>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-4">
        <x-inputs.date name="end_date" label="End Date"
            value="{{ old('end_date', $editing ? optional($enrollment->end_date)->format('Y-m-d') : '') }}"
            max="255" required></x-inputs.date>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-4">
        <x-inputs.number name="fee" label="Fee" :value="old('fee', $editing ? $enrollment->fee : '')" max="255"
            placeholder="Fee"></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-4">
        <x-inputs.text name="duration" label="Duration" :value="old('duration', $editing ? $enrollment->duration : '')" maxlength="255"
            placeholder="Duration"></x-inputs.text>
    </x-inputs.group>
</div>
