@php $editing = isset($user) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12 mt-3 col-lg-5">
        <x-inputs.text name="name" label="User Full Name *" :value="old('name', $editing ? $user->name : '')" maxlength="255" placeholder="Name"
            required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mt-3 col-lg-4">
        <x-inputs.email name="email" label="User Email*" :value="old('email', $editing ? $user->email : '')" maxlength="255" placeholder="Email"
            required></x-inputs.email>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mt-3 col-lg-3">
        <x-inputs.text name="phone" label="Phone Number*" :value="old('phone', $editing ? $user->phone : '')" maxlength="255"
            placeholder="Phone"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mt-3 col-lg-5">
        <x-inputs.text name="address" label="User Address" :value="old('address', $editing ? $user->address : '')" maxlength="255"
            placeholder="Address"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mt-3 col-lg-4">
        <x-inputs.password name="password" label="User Password" maxlength="255" placeholder="Password*"
            :required="!$editing"></x-inputs.password>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mt-3 col-lg-3">
        <div x-data="imageViewer('{{ $editing && $user->image ? url(\Storage::url('profile/' . $user->image)) : '' }}')">
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

    <div class="form-group col-sm-12 mt-3">
        <h4>Assign @lang('crud.roles.name')</h4>
        @foreach ($roles as $role)
            <div class="form-check form-check-inline">
                <x-inputs.radio id="role{{ $role->id }}" name="davinci_role_id" label="{{ ucfirst($role->name) }}"
                    value="{{ $role->id }}" :checked="isset($user) ? $user->hasRole($role->name) : false" :add-hidden-value="false"></x-inputs.radio>
            </div>
        @endforeach
    </div>

    @if (auth()->user()->hasRole('system-admin') && isset($branches))
        <x-inputs.group class="col-sm-12 mt-3 col-lg-6">
            <x-inputs.select name="branch_id" label="Branch">
                @php $selected = old('branch_id', ($editing ? $user->branch_id : '')) @endphp
                <option value="" {{ empty($selected) ? 'selected' : '' }}>No branch</option>
                @foreach ($branches as $value => $label)
                    <option value="{{ $value }}" {{ (string) $selected === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </x-inputs.select>
        </x-inputs.group>
    @endif

</div>
