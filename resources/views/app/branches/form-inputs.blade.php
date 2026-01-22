@php $editing = isset($branch) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12 col-lg-6">
        <x-inputs.text name="name" label="Branch  Name *" :value="old('name', $editing ? $branch->name : '')" maxlength="255"
            placeholder="Eg. Biafra Branch" required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-6">
        <x-inputs.text name="location" label="Branch  Location" :value="old('location', $editing ? $branch->location : '')" maxlength="255"
            placeholder="Eg. Dar es salaam"></x-inputs.text>
    </x-inputs.group>
</div>
