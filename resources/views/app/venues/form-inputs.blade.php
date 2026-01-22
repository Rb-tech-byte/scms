@php $editing = isset($venue) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12 col-lg-6">
        <x-inputs.text name="name" label="Venue  Name *" :value="old('name', $editing ? $venue->name : '')" maxlength="255" placeholder="Enter venue name"
            required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-6">
        <x-inputs.select name="branch_id" label="Choose Branch *" required>
            @php $selected = old('branch_id', ($editing ? $venue->branch_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Branch</option>
            @foreach ($branches as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>
