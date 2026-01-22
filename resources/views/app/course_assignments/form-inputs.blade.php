@php $editing = isset($courseAssignment) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.select name="tutor_id" label="Tutor" class="select2" id="tutor_id" required>
            @php $selected = old('tutor_id', ($editing ? $courseAssignment->tutor_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Tutor</option>
            @foreach ($tutors as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.select name="branch_id" id="branch_id" label="Branch" class="select2" required>
            @php $selected = old('branch_id', ($editing ? $courseAssignment->branch_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Branch</option>
            @foreach ($branches as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.select name="venue_id" id="venue_id" label="Venue" class="select2" required>
            @php $selected = old('venue_id', ($editing ? $courseAssignment->venue_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Venue</option>
            @foreach ($venues as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.select name="course_id" label="Course" required>
            @php $selected = old('course_id', ($editing ? $courseAssignment->course_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Course</option>
            @foreach ($courses as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.date name="date" label="Date"
            value="{{ old('date', $editing ? optional($courseAssignment->date)->format('Y-m-d') : '') }}"
            max="255" required></x-inputs.date>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.time name="start_time" label="Start Time" :value="old('start_time', $editing ? $courseAssignment->start_time : '')" maxlength="255" placeholder="Start Time"
            required></x-inputs.time>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.time name="end_time" label="End Time" :value="old('end_time', $editing ? $courseAssignment->end_time : '')" maxlength="255" placeholder="End Time"
            required></x-inputs.time>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mt-5 col-lg-3">
        <x-inputs.checkbox name="active" label="Active" :checked="old('active', $editing ? $courseAssignment->active : 0)"></x-inputs.checkbox>
    </x-inputs.group>
</div>

@push('js')
    <script>
        $(document).ready(function() {
            $('#tutor_id').on('change', function() {
                var tutor_id = $(this).val();
                if (tutor_id) {
                    $.ajax({
                        url: "{{ URL::to('get-branch') }}/" + tutor_id,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            $('#branch_id').empty();
                            $('#branch_id').append('<option value="' + data.branch.id + '">' + data.branch.name + '</option>');
                            $('#venue_id').empty();
                            $('#venue_id').append('<option value="">-- Assign ' + data.branch.name + ' Venue --</option>');
                            $.each(data.venues, function(index, venue) {
                                $('#venue_id').append('<option value="' + venue.id + '">' + venue.name + '</option>');
                            });
                        },
                    });
                } else {
                    $('#branch_id').empty();
                    $('#branch_id').append('<option value="">-- Select Branch --</option>');
                    $('#venue_id').empty();
                    $('#venue_id').append('<option value="">-- Select Venue --</option>');
                }
            });
        });
    </script>
@endpush
