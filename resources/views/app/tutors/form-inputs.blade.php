@php $editing = isset($tutor) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12 col-lg-4 mb-3">
        <x-inputs.text name="name" label="Full Name*" :value="old('name', $editing ? $tutor->name : '')" placeholder="Name" required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-4 mb-3">
        <x-inputs.email name="email" label="Email*" :value="old('email', $editing ? $tutor->email : '')" maxlength="255" placeholder="Email"
            required></x-inputs.email>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-4 mb-3">
        <x-inputs.text name="phone" label="Phone Number" :value="old('phone', $editing ? $tutor->phone : '')" placeholder="Phone"
            required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-3 mb-3">
        <x-inputs.text name="address" label="Address" :value="old('address', $editing ? $tutor->address : '')" maxlength="255"
            placeholder="Address"></x-inputs.text>
    </x-inputs.group>

    @if (auth()->user()->hasRole('admin') ||
                auth()->user()->hasRole('system-admin') || auth()->user()->hasRole('branch-admin') )
    <x-inputs.group class="col-sm-12 col-lg-3 mb-3">
        <x-inputs.select name="branch_id" label="Tutor's Branch*" required>
            @php $selected = old('branch_id', ($editing ? $tutor->branch_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Branch</option>
            @foreach ($branches as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
    @endif

    <x-inputs.group class="col-sm-12 col-lg-3">
        <div x-data="imageViewer('{{ $editing && $tutor->user->image ? url(\Storage::url('profile/' . $tutor->user->image)) : '' }}')">
            <x-inputs.partials.label name="image" label="User Image"></x-inputs.partials.label><br />


            <template x-if="imageUrl">
                <img :src="imageUrl" class="object-cover rounded border border-gray-200"
                    style="width: 100px; height: 100px;" />
            </template>


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



    @if ($editing)
        <x-inputs.group class="col-sm-12 mb-3 col-lg-2">
            <x-inputs.password name="password" label="Tutor Password (Leave empty if not changed)" maxlength="255"
                placeholder="Password*" :required="!$editing"></x-inputs.password>
        </x-inputs.group>
        <x-inputs.group class="col-sm-12 mb-3 col-lg-2">
            <x-inputs.password name="password_confirmation" label="Password Confirm" maxlength="255"
                placeholder="Confirm Password*" :required="!$editing"></x-inputs.password>
        </x-inputs.group>

    @endif

    @if (auth()->user()->hasRole(['admin','system-admin','branch-admin']))

<div class="form-group col-sm-12 col-lg-3 mt-4">
    <h4>Assign @lang('crud.roles.name')</h4>

    @foreach ($roles as $role)
        <div>
            <x-inputs.radio id="role{{ $role->id }}" name="roles"
                label="{{ ucfirst($role->name) }}" value="{{ $role->id }}" :checked="isset($tutor->user) ? $tutor->user->hasRole($role->name) : false"
                :add-hidden-value="false"></x-inputs.checkbox>
        </div>
    @endforeach
</div>
@endif
    @if (auth()->user()->hasRole('admin') ||
                auth()->user()->hasRole('system-admin'))
    <x-inputs.group class="col-sm-12 col-lg-3 mt-5">
        <x-inputs.checkbox name="active" label="Active" :checked="old('active', $editing ? $tutor->active : 1)"></x-inputs.checkbox>
    </x-inputs.group>
    @endif
</div>
