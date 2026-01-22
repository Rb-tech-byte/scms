@php $editing = isset($country) @endphp

<div class="row">
    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="nicename" label="Nicename" :value="old('nicename', $editing ? $country->nicename : '')" maxlength="255"
            placeholder="Nicename"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="iso" label="Iso" :value="old('iso', $editing ? $country->iso : '')" maxlength="255" placeholder="Iso"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.text name="iso3" label="Iso3" :value="old('iso3', $editing ? $country->iso3 : '')" maxlength="255"
            placeholder="Iso3"></x-inputs.text>
    </x-inputs.group>
</div>
