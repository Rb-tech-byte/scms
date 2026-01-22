@php $editing = isset($setting) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="system_name"
            label="System Name"
            :value="old('system_name', ($editing ? $setting->system_name : ''))"
            maxlength="255"
            placeholder="System Name"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="system_logo"
            label="System Logo"
            :value="old('system_logo', ($editing ? $setting->system_logo : ''))"
            maxlength="255"
            placeholder="System Logo"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.checkbox
            name="allow_dark_sidebar"
            label="Allow Dark Sidebar"
            :checked="old('allow_dark_sidebar', ($editing ? $setting->allow_dark_sidebar : 0))"
        ></x-inputs.checkbox>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.checkbox
            name="allow_message"
            label="Allow Message"
            :checked="old('allow_message', ($editing ? $setting->allow_message : 0))"
        ></x-inputs.checkbox>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.checkbox
            name="allow_email"
            label="Allow Email"
            :checked="old('allow_email', ($editing ? $setting->allow_email : 0))"
        ></x-inputs.checkbox>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.checkbox
            name="enable_upload_receipt"
            label="Enable Upload Receipt"
            :checked="old('enable_upload_receipt', ($editing ? $setting->enable_upload_receipt : 0))"
        ></x-inputs.checkbox>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="mail_mailer"
            label="Mail Mailer"
            :value="old('mail_mailer', ($editing ? $setting->mail_mailer : ''))"
            maxlength="255"
            placeholder="Mail Mailer"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="mail_host"
            label="Mail Host"
            :value="old('mail_host', ($editing ? $setting->mail_host : ''))"
            maxlength="255"
            placeholder="Mail Host"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="mail_port"
            label="Mail Port"
            :value="old('mail_port', ($editing ? $setting->mail_port : ''))"
            maxlength="255"
            placeholder="Mail Port"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="mail_username"
            label="Mail Username"
            :value="old('mail_username', ($editing ? $setting->mail_username : ''))"
            maxlength="255"
            placeholder="Mail Username"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="mail_password"
            label="Mail Password"
            :value="old('mail_password', ($editing ? $setting->mail_password : ''))"
            maxlength="255"
            placeholder="Mail Password"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="mail_encryption"
            label="Mail Encryption"
            :value="old('mail_encryption', ($editing ? $setting->mail_encryption : ''))"
            maxlength="255"
            placeholder="Mail Encryption"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="mail_from_address"
            label="Mail From Address"
            :value="old('mail_from_address', ($editing ? $setting->mail_from_address : ''))"
            maxlength="255"
            placeholder="Mail From Address"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.textarea
            name="site_description"
            label="Site Description"
            maxlength="255"
            >{{ old('site_description', ($editing ? $setting->site_description :
            '')) }}</x-inputs.textarea
        >
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="site_contact_email"
            label="Site Contact Email"
            :value="old('site_contact_email', ($editing ? $setting->site_contact_email : ''))"
            maxlength="255"
            placeholder="Site Contact Email"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="site_contact_number"
            label="Site Contact Number"
            :value="old('site_contact_number', ($editing ? $setting->site_contact_number : ''))"
            maxlength="255"
            placeholder="Site Contact Number"
        ></x-inputs.text>
    </x-inputs.group>
</div>
