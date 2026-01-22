@php $editing = isset($applicant) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12 mt-3 col-lg-4">
        <x-inputs.text name="full_name" label="Full Name *" :value="old('full_name', $editing ? $applicant->full_name : '')" maxlength="255" placeholder="Full Name"
            required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mt-3 col-lg-4">
        <x-inputs.text name="home_address" label="Home Address*" :value="old('home_address', $editing ? $applicant->home_address : '')" maxlength="255"
            placeholder="Home Address"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mt-3 col-lg-4">
        <x-inputs.select name="country_id" label="Country *" required>
            @php $selected = old('country_id', ($editing ? $applicant->country_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Country</option>
            @foreach ($countries as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mt-3 col-lg-4">
        <x-inputs.text name="office_of_work" label="Office Of Work" :value="old('office_of_work', $editing ? $applicant->office_of_work : '')" maxlength="255"
            placeholder="Office Of Work"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mt-3 col-lg-4">
        <x-inputs.text name="occupation" label="Occupation" :value="old('occupation', $editing ? $applicant->occupation : '')" maxlength="255"
            placeholder="Occupation"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mt-3 col-lg-4">
        <x-inputs.text name="mobile" label="Mobile No *" :value="old('mobile', $editing ? $applicant->mobile : '')" maxlength="255" placeholder="Mobile"
            required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mt-3 col-lg-4">
        <x-inputs.email name="email" label="Email" :value="old('email', $editing ? $applicant->email : '')" maxlength="255"
            placeholder="Email"></x-inputs.email>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mt-3 col-lg-4">
        <x-inputs.text name="name_of_sponsor" label="Name Of Sponsor/ Guardian" :value="old('name_of_sponsor', $editing ? $applicant->name_of_sponsor : '')" maxlength="255"
            placeholder="Name Of Sponsor/ Guardian"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mt-3 col-lg-4">
        <x-inputs.text name="phone_2" label="His/Her Mobile No" :value="old('phone_2', $editing ? $applicant->phone_2 : '')" maxlength="255"
            placeholder="Phone 2"></x-inputs.text>
    </x-inputs.group>



    {{-- <x-inputs.group class="col-sm-12 mt-3 col-lg-4">
        <x-inputs.text name="education_level" label="Education Level" :value="old('education_level', $editing ? $applicant->education_level : '')" maxlength="255"
            placeholder="Education Level" required></x-inputs.text>
    </x-inputs.group> --}}
    <x-inputs.group class="col-sm-12 mt-3 col-lg-8">
        <div class="mb-2">
            <label for="education_level">Education Level <span style="color: red;font-size: 20px;">*</span></label>
        </div>


        @php
            $educationLevels = [
                '1' => 'Std VII',
                '2' => 'Form 4',
                '3' => 'Form VI',
                '4' => 'College',
                '5' => 'University',
                '6' => 'Other',
            ];
            $selectedLevel = old('education_level', $editing ? $applicant->education_level : '');
        @endphp

        @foreach ($educationLevels as $value => $label)
            <div class="form-check form-check-inline square-radio">
                <label class="form-check-label" for="education_level_{{ $value }}">{{ $label }}</label>
                <input class="form-check-input" type="radio" name="education_level"
                    id="education_level_{{ $value }}" value="{{ $value }}"
                    {{ $selectedLevel == $value ? 'checked' : '' }}>

            </div>
        @endforeach
    </x-inputs.group>
    <x-inputs.group class="col-sm-12 mt-3 col-lg-4">
        <div x-data="imageViewer('{{ $editing && $applicant->user->image ? url(\Storage::url('profile/' . $applicant->user->image)) : '' }}')">
            <x-inputs.partials.label name="image" label="User Image"></x-inputs.partials.label><br />

            <!-- Show the image -->
            <template x-if="imageUrl">
                <img :src="imageUrl" class="object-cover rounded border border-gray-200"
                    style="width: 100px; height: 100px;" />
            </template>

            <!-- Show the gray box when image is not available -->
            <template x-if="!imageUrl">
                <div class="border rounded border-gray-200 bg-gray-100" style="width: 100px; height: 100px;"></div>
            </template>

            <div class="mt-2">
                <input type="file" name="image" id="image" @change="fileChosen" />
            </div>

            @error('image')
                @include('components.inputs.partials.error')
            @enderror
        </div>
    </x-inputs.group>
</div>
