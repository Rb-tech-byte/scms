@extends('layouts.app')
@section('title', 'Show Student')
@section('header')
    {!! $generatePageHeader('System Setting', 'settings.index', 'setting', 'Setting', 'plus') !!}
@endsection
@section('content')
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
                        <li class="nav-item">
                            <a href="#payments" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0">
                                PAYMENTS
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#settings" data-bs-toggle="tab" aria-expanded="false" class="nav-link rounded-0">
                                Settings
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="generalsettings">

                            <h5 class="text-uppercase"><i class="mdi mdi-briefcase me-1"></i>

                                <div class="card-body">
                                    <x-form
                                        method="POST"
                                        action="{{ route('settings.store') }}"
                                        class="mt-4"
                                    >
                                        <x-inputs.group class="col-sm-12 mb-3">
                                            <x-inputs.text
                                                name="system_name"
                                                label="System Name"
                                                :value="old('system_name', ($editing ? $setting->system_name : ''))"
                                                maxlength="255"
                                                placeholder="System Name"
                                            ></x-inputs.text>
                                        </x-inputs.group>

                                        <x-inputs.group class="col-sm-12 mb-3">
                                            <x-inputs.text
                                                name="system_logo"
                                                label="System Logo"
                                                :value="old('system_logo', ($editing ? $setting->system_logo : ''))"
                                                maxlength="255"
                                                placeholder="System Logo"
                                            ></x-inputs.text>
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

                            </div>


                        </div>
                        <div class="tab-content">
                            <div class="tab-pane active" id="payments">

                                <div class="card-body">

                                </div>

                            </div>
                        </div>
                        <!-- end timeline content-->

                        <div class="tab-pane" id="settings">

                        </div>
                        <!-- end settings content-->

                    </div> <!-- end tab-content -->
                </div> <!-- end card body -->
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div>
@endsection
