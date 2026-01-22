@php $editing = isset($notification) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12 col-lg-6">
        <x-inputs.text name="applicant_id" label="Applicant Id" :value="old('applicant_id', $editing ? $notification->applicant_id : '')" maxlength="255"
            placeholder="Applicant Id"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-6">
        <x-inputs.text name="user_id" label="User Id" :value="old('user_id', $editing ? $notification->user_id : '')" maxlength="255"
            placeholder="User Id"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.textarea name="message" label="Message" maxlength="255"
            required>{{ old('message', $editing ? $notification->message : '') }}</x-inputs.textarea>
    </x-inputs.group>
</div>
