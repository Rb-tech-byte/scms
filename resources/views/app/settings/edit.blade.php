@extends('layouts.app')
@section('title', 'Show Student')
@section('header')
    {!! $generatePageHeader('System Setting', 'settings.index', 'setting', 'Setting', 'plus') !!}
@endsection
@section('content')
    @push('css')
        <script src="https://unpkg.com/alpinejs@3.10.2/dist/cdn.min.js" defer></script>
        @endpush
    @php $editing = isset($setting) @endphp
    <div class="row">


        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                        <li class="nav-item">
                            <a href="#generalsettings" data-bs-toggle="tab" aria-expanded="false" class="nav-link rounded-0 active">
                                GENERAL SETTING
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#timeline" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0">
                                MAIL SETTING
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="generalsettings">

                            <h5 class="text-uppercase"><i class="mdi mdi-briefcase me-1"></i>

                                <div class="card-body">
                                    <x-form
                                        method="PUT"
                                        action="{{ route('settings.update',$setting) }}"
                                        has-files
                                        class="mt-4"
                                    >
                                        <x-inputs.group class="col-sm-12 mb-3">
                                            <x-inputs.text
                                                name="system_name"
                                                label="System Name"
                                                :value="old('system_name', ($editing ? $setting->system_name : ''))"
                                                maxlength="16"
                                                placeholder="System Name"
                                            ></x-inputs.text>
                                        </x-inputs.group>

                                        <x-inputs.group class="col-sm-4 my-4">
                                            <div x-data="imageViewer('{{ $editing && $setting->system_logo ? url(\Storage::url($setting->system_logo)) : '' }}')">
                                                <x-inputs.partials.label name="image" label="System Logo"></x-inputs.partials.label><br />

                                                <!-- Show the image -->
                                                <template x-if="imageUrl">
                                                    <img :src="imageUrl" class="object-cover rounded border border-gray-200"
                                                         style="width: 100px; height: 100px;" />
                                                </template>

                                                <!-- Show the gray box when image is not available -->
                                                <template x-if="!imageUrl">
                                                    <div class="border rounded border-gray-200 bg-gray-100" style="width: 240px; height: 32px;"></div>
                                                </template>

                                                <div class="mt-2">
                                                    <input type="file" name="system_logo" id="image" @change="fileChosen" />
                                                </div>

                                                @error('system_logo')
                                                @include('components.inputs.partials.error')
                                                @enderror
                                            </div>
                                        </x-inputs.group>

                                        <x-inputs.group class="col-sm-12 mb-3">
                                            <x-inputs.checkbox
                                                name="allow_dark_sidebar"
                                                label="Allow Dark Sidebar"
                                                :checked="old('allow_dark_sidebar', ($editing ? $setting->allow_dark_sidebar : 0))"
                                            ></x-inputs.checkbox>
                                        </x-inputs.group>

                                        <x-inputs.group class="col-sm-12 mb-4">
                                            <x-inputs.checkbox
                                                name="allow_message"
                                                label="Allow Message"
                                                :checked="old('allow_message', ($editing ? $setting->allow_message : 0))"
                                            ></x-inputs.checkbox>
                                        </x-inputs.group>

                                        <x-inputs.group class="col-sm-12 my-3">
                                            <x-inputs.checkbox
                                                name="enable_upload_receipt"
                                                label="Enable Upload Receipt"
                                                :checked="old('enable_upload_receipt', ($editing ? $setting->enable_upload_receipt : 0))"
                                            ></x-inputs.checkbox>
                                        </x-inputs.group>

                                        <x-inputs.group class="col-sm-12 my-3">
                                            <x-inputs.textarea
                                                name="site_description"
                                                label="Site Description"
                                                maxlength="255"
                                            >{{ old('site_description', ($editing ? $setting->site_description :
            '')) }}</x-inputs.textarea
                                            >
                                        </x-inputs.group>

                                        <x-inputs.group class="col-sm-12 my-3">
                                            <x-inputs.text
                                                name="site_contact_email"
                                                label="Site Contact Email"
                                                :value="old('site_contact_email', ($editing ? $setting->site_contact_email : ''))"
                                                maxlength="255"
                                                placeholder="Site Contact Email"
                                            ></x-inputs.text>
                                        </x-inputs.group>

                                        <x-inputs.group class="col-sm-12 my-3">
                                            <x-inputs.text
                                                name="site_contact_number"
                                                label="Site Contact Number"
                                                :value="old('site_contact_number', ($editing ? $setting->site_contact_number : ''))"
                                                maxlength="255"
                                                placeholder="Site Contact Number"
                                            ></x-inputs.text>
                                        </x-inputs.group>
                                        <button type="submit" class="btn btn-primary float-right">
                                            <i class="icon ion-md-save"></i>
                                            @lang('crud.common.update')
                                        </button>
                                    </x-form>
                                </div>


                        </div> <!-- end tab-pane -->
                        <!-- end about me section content -->

                        <div class="tab-pane show" id="timeline">
                            {{-- Fetch all invoices of this applicant and display them here in table  --}}

                            <div class="card-body">

                                <x-form
                                    method="PUT"
                                    action="{{ route('settings.update',$setting) }}"
                                    class="mt-4"
                                >
                                    <x-inputs.group class="col-sm-12 my-3">
                                        <x-inputs.checkbox
                                            name="allow_email"
                                            label="Allow Email"
                                            :checked="old('allow_email', ($editing ? $setting->allow_email : 0))"
                                        ></x-inputs.checkbox>
                                    </x-inputs.group>


                                    <x-inputs.group class="col-sm-12 my-3">
                                        <x-inputs.text
                                            name="mail_mailer"
                                            label="Mail Mailer"
                                            :value="old('mail_mailer', ($editing ? $setting->mail_mailer : ''))"
                                            maxlength="255"
                                            placeholder="Mail Mailer"
                                        ></x-inputs.text>
                                    </x-inputs.group>

                                    <x-inputs.group class="col-sm-12 my-3">
                                        <x-inputs.text
                                            name="mail_host"
                                            label="Mail Host"
                                            :value="old('mail_host', ($editing ? $setting->mail_host : ''))"
                                            maxlength="255"
                                            placeholder="Mail Host"
                                        ></x-inputs.text>
                                    </x-inputs.group>

                                    <x-inputs.group class="col-sm-12 my-3">
                                        <x-inputs.text
                                            name="mail_port"
                                            label="Mail Port"
                                            :value="old('mail_port', ($editing ? $setting->mail_port : ''))"
                                            maxlength="255"
                                            placeholder="Mail Port"
                                        ></x-inputs.text>
                                    </x-inputs.group>

                                    <x-inputs.group class="col-sm-12 my-3">
                                        <x-inputs.text
                                            name="mail_username"
                                            label="Mail Username"
                                            :value="old('mail_username', ($editing ? $setting->mail_username : ''))"
                                            maxlength="255"
                                            placeholder="Mail Username"
                                        ></x-inputs.text>
                                    </x-inputs.group>

                                    <x-inputs.group class="col-sm-12 my-3">
                                        <x-inputs.text
                                            name="mail_password"
                                            label="Mail Password"
                                            :value="old('mail_password', ($editing ? $setting->mail_password : ''))"
                                            maxlength="255"
                                            placeholder="Mail Password"
                                        ></x-inputs.text>
                                    </x-inputs.group>

                                    <x-inputs.group class="col-sm-12 my-3">
                                        <x-inputs.text
                                            name="mail_encryption"
                                            label="Mail Encryption"
                                            :value="old('mail_encryption', ($editing ? $setting->mail_encryption : ''))"
                                            maxlength="255"
                                            placeholder="Mail Encryption"
                                        ></x-inputs.text>
                                    </x-inputs.group>

                                    <x-inputs.group class="col-sm-12 my-3">
                                        <x-inputs.text
                                            name="mail_from_address"
                                            label="Mail From Address"
                                            :value="old('mail_from_address', ($editing ? $setting->mail_from_address : ''))"
                                            maxlength="255"
                                            placeholder="Mail From Address"
                                        ></x-inputs.text>
                                    </x-inputs.group>
                                    <button type="submit" class="btn btn-primary float-right">
                                        <i class="icon ion-md-save"></i>
                                        @lang('crud.common.update')
                                    </button>
                                </x-form>
                            </div>


                        </div>


                    </div> <!-- end tab-content -->
                </div> <!-- end card body -->
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div>
@endsection
