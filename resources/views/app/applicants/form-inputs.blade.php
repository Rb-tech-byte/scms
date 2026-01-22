@php
    $editing = isset($applicant);
    $settings = \App\Models\Setting::first();
@endphp

<div class="row">
    <div class="col-sm-12 mb-3 col-lg-8">
        <h3>1.Applicant Information</h3>
    </div>
</div>
<div class="row">
    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.text name="full_name" label="Full Name *" :value="old('full_name', $editing ? $applicant->full_name : '')" maxlength="255" placeholder="Full Name"
            required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.text name="home_address" label="Home Address*" :value="old('home_address', $editing ? $applicant->home_address : '')" maxlength="255"
            placeholder="Home Address"></x-inputs.text>
    </x-inputs.group>

    {{-- <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.select name="country_id" class="select" label="Nationality *" required>
            @php $selected = old('country_id', ($editing ? $applicant->country_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Country</option>
            @foreach ($countries as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group> --}}
    {{-- <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.select name="country_id" class="itemSearch" label="Country *" required>
            @php $selected = old('country_id', ($editing ? $applicant->country_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Country</option>
        </x-inputs.select>
    </x-inputs.group> --}}

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.select name="country_id" class="itemSearch" label="Country *" required>
            @php
                $selected = old('country_id', $editing ? $applicant->country_id : '');
                $defaultCountryId = array_search('TANZANIA', $countries->toArray());
            @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Country</option>
            @foreach ($countries as $id => $name)
                <option value="{{ $id }}"
                    {{ $selected == $id || $defaultCountryId == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.text name="office_of_work" label="Office Of Work" :value="old('office_of_work', $editing ? $applicant->office_of_work : '')" maxlength="255"
            placeholder="Office Of Work"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.text name="occupation" label="Occupation" :value="old('occupation', $editing ? $applicant->occupation : '')" maxlength="255"
            placeholder="Occupation"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.text name="mobile" label="Mobile No *" :value="old('mobile', $editing ? $applicant->mobile : '')" maxlength="255" placeholder="Mobile"
            required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.email name="email" label="Email" :value="old('email', $editing ? $applicant->email : '')" maxlength="255"
            placeholder="Email"></x-inputs.email>
    </x-inputs.group>



    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.text name="name_of_sponsor" label="Name Of Sponsor/ Guardian" :value="old('name_of_sponsor', $editing ? $applicant->name_of_sponsor : '')" maxlength="255"
            placeholder="Name Of Sponsor/ Guardian"></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.text name="phone_2" label="His/Her Mobile No" :value="old('phone_2', $editing ? $applicant->phone_2 : '')" maxlength="255"
            placeholder="Phone 2"></x-inputs.text>
    </x-inputs.group>



    <x-inputs.group class="col-sm-12 mb-3 col-lg-12">
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
</div>
<div class="row">
    <div class="col-sm-12 mb-3 col-lg-8">
        <h3>1.1 Staff Student Validation</h3>
    </div>
    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.checkbox id="is_staff_student" name="is_staff_student" label="Is Staff Student (50% Fee, Auto Complete Payment)" value="1" :checked="old('is_staff_student') == '1'" />
    </x-inputs.group>
    <x-inputs.group class="col-sm-12 mb-3 col-lg-4" id="parent_staff_id_group" style="display:none;">
        <x-inputs.file name="parent_staff_id" label="Upload Parent Staff ID (pdf,jpg,png,jpeg,webp)" accept=".pdf,.png,.jpg,.jpeg" />
    </x-inputs.group>
    @push('js')
    <script>
        $(document).ready(function(){
            function toggleParentId() {
                if ($('#is_staff_student').is(':checked')) {
                    $('#parent_staff_id_group').show();
                } else {
                    $('#parent_staff_id_group').hide();
                }
            }
            toggleParentId();
            $('#is_staff_student').on('change', toggleParentId);
        });
    </script>
    @endpush
</div>
<div class="row">
    <div class="col-sm-12 mb-3 col-lg-8">
        <h3>2. Course Details</h3>
    </div>
</div>

@php $editing = isset($enrollment) @endphp

<div class="row">

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.select name="course_id" id="course_id" label="Course Title *" class="select2" required>
            <option value="" disabled selected>Please select the Course</option>
            @foreach ($courses as $value => $label)
                <option value="{{ $value }}" data-fee="" {{ old('course_id') == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.select name="session" class="select2" label="Session *">
            @php $selected = old('session', ($editing ? $enrollment->session : 'morning')) @endphp
            <option value="morning" {{ $selected == 'morning' ? 'selected' : '' }}>Morning</option>
            <option value="afternoon" {{ $selected == 'afternoon' ? 'selected' : '' }}>afternoon</option>
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.select name="venue_id" class="select2" label="Venue *" required>
            @php $selected = old('venue_id', ($editing ? $enrollment->venue_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Venue</option>
            @foreach ($venues as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4 ">
        <x-inputs.number name="fee" id="fee" label="Course Fee *" :value="old('fee', $editing ? $enrollment->fee : '')"
            class="form-control-solid fee" placeholder="Fee" readonly></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.date name="start_date" label="Start Date * (mm/dd/yyyy))"
            value="{{ old('start_date', $editing ? optional($enrollment->start_date)->format('Y-m-d') : '') }}"
            id="start_date" required></x-inputs.date>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 mb-3 col-lg-4">
        <x-inputs.date name="end_date" label="End Date * (mm/dd/yyyy)"
            value="{{ old('end_date', $editing ? optional($enrollment->end_date)->format('Y-m-d') : '') }}"
            id="end_date" required readonly></x-inputs.date>
    </x-inputs.group>
    <x-inputs.group class="col-sm-12 mb-3 col-lg-12">
        <div class="mb-2">
            <label for="duration">Duration <span style="color: red;font-size: 20px;">*</span> : </label>
        </div>
        @php
            $durations = [
                '1' => '1 Week',
                '2' => '2 Week',
                '3' => '3 Week',
                '4' => '4 Week',
                '5' => '5 Week',
                '6' => '6 Week',
                '7' => '7 Week',
                '8' => '8 Week',
                '16' => '16 Week',
                '9' => 'other',
            ];
            $selectedLevel = old('duration', $editing ? $applicant->duration : '');
        @endphp

        @foreach ($durations as $value => $label)
            <div class="form-check form-check-inline square-radio">
                <label class="form-check-label" for="duration_{{ $value }}">{{ $label }}</label>
                <input class="form-check-input" type="radio" name="duration" id="duration_{{ $value }}"
                    value="{{ $value }}" {{ $selectedLevel == $value ? 'checked' : '' }}>
            </div>
        @endforeach

        <div id="other_duration" style="display: none;">
            <label for="custom_duration">Custom Duration in (weeks): </label>
            <input type="number" name="custom_duration" id="custom_duration" value="{{ old('custom_duration') }}">
        </div>
    </x-inputs.group>
    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var durationRadios = document.querySelectorAll('input[name="duration"]');
                var customDurationInput = document.getElementById('other_duration');
                for (var i = 0; i < durationRadios.length; i++) {
                    durationRadios[i].addEventListener('change', function() {
                        if (this.value === '9') {
                            customDurationInput.style.display = 'block';
                        } else {
                            customDurationInput.style.display = 'none';
                        }
                    });
                }
            });
        </script>
    @endpush
</div>

<div class="row mt-3">
    <div class="col-sm-12 mb-3 col-lg-8">
        <h3>3. Payment Details</h3>
    </div>
</div>

<div class="row">
    <x-inputs.group class="col-sm-12 col-lg-3">
        <x-inputs.text name="amount" class="form-control-solid fee payment" id="payment_amount" label="Amount"
            :value="old('amount', $editing ? $payment->amount : '')" placeholder="Amount" readonly></x-inputs.text>
            <small id="if_partial_payment_is_checked_text" class="text-danger"></small>
    </x-inputs.group>


    <div class="form-group col-sm-4 mt-3">
        <h4>Installment</h4>
        <div class="radio-wrapper d-flex">
            <div>
                <x-inputs.radio id="installment_quarter" class="installment_type_class" name="installment_type" label="Quarter (25%)" value="quarter" :add-hidden-value="false" />
            </div>

            <div class="mx-3">
                <x-inputs.radio id="installment_half" class="installment_type_class" name="installment_type" label="Half (50%)" value="half" :add-hidden-value="false" />
            </div>

            <div class="mx-3">
                <x-inputs.radio id="installment_full" class="installment_type_class" name="installment_type" label="Full (100%)" value="full" :add-hidden-value="false" />
            </div>

        </div>

    </div>

    <div class="form-group col-sm-4 mt-3" id="show_only_if_course_selected_has_discount" style="display: none">
        <h4 class="text-success">Selected Course Has Discount</h4>
        <div class="radio-wrapper d-flex">
            <div>
                <x-inputs.radio id="discount_yes" class="apply_discount" name="apply_discount"
                    label="Apply Discount" value="1" :add-hidden-value="false" />
            </div>

            <div class="mx-3">
                <x-inputs.radio id="discount_no" class="apply_discount" name="apply_discount" label="No discount"
                    value="0" checked :add-hidden-value="false" />
            </div>

        </div>

    </div>

    <x-inputs.group class="col-sm-12 col-lg-3">
        <x-inputs.datetime name="date" label="Date"
            value="{{ old('date', $editing ? optional($payment->date)->format('Y-m-d\TH:i:s') : date('Y-m-d\TH:i:s')) }}"
            required readonly></x-inputs.datetime>
    </x-inputs.group>
    <div class="row" id="if_discount_checked_row" style="display: none;">
        <div class="col-sm-12 col-lg-12">
            <small class="text-red">Original Course fee will be decucted by <span id="show_discount_amount"></span>
            </small>
        </div>
        <div class="col-sm-12 col-lg-12 mt-3">
            <h6 class="text-success">Tell us Why Applicant has Discount?</h6>
        </div>
        <div class="col-sm-12 col-lg-12">
            <div class="radio-group">
                <label for="relative">1. Staff Child</label>
                <input type="radio" name="discount_reason" value="staff_child">
            </div>
            <div class="radio-group">
                <label for="relative">2. Staff Member</label>
                <input type="radio" name="discount_reason" value="staff_member">
            </div>
            <div class="radio-group">
                <label for="relative">3. OUT student</label>
                <input type="radio" name="discount_reason" value="out_student">
            </div>
        </div>
        <div class="col-sm-12 mb-3">
            <label for="relative">Discount attachment (ID Card)</label>
            <div>
                <input type="file" accept=".pdf,.png,.jpg,.jpeg" name="discount_attachment" accept="pdf,png,jpeg,jpg,webp" >
            </div>
            
        </div>
    </div>
    <div class="row">
        <x-inputs.group class="col-sm-12 col-lg-5 my-3">
            <x-inputs.file name="receipt" label="Receipt Attachment (pdf,jpg,png,jpeg,webp)" :value="old('receipt', $editing ? $payment->receipt : '')"
                accept=".pdf,.png,.jpg,.jpeg" style="background: #721c24;" placeholder="Payment Attachment"></x-inputs.file>
        </x-inputs.group>
        <x-inputs.group class="col-sm-12 col-lg-4 my-3">
            <x-inputs.text name="receipt_number" label="Receipt Number *" :value="old('receipt_number', $editing ? ($payment->receipt_number ?? '') : '')" maxlength="255" placeholder="e.g. OUT-0123" required></x-inputs.text>
        </x-inputs.group>
    </div>

    @push('js')
        <script>
            $(document).ready(function() {
                $('.itemSearch').select2({
                    theme: 'bootstrap-5',
                    ajax: {
                        url: '{{ route('countries.search') }}',
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: item.name,
                                        id: item.id
                                    }
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 1
                });
            });
        </script>
    @endpush
    @push('js')
        <script>
            $(document).ready(function() {
                var courseSelect = $('#course_id');
                var feeInput = $('.fee');


                courseSelect.on('change', function() {
                    var selectedOption = $('option:selected', this);
                    var selectedCourseId = selectedOption.val();
                    if (selectedCourseId) {
                        var url = '{{ route('fees.get', ':id') }}';
                        url = url.replace(':id', selectedCourseId);
                        $.ajax({
                            url: url,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                feeInput.val(data.fee);
                                var duration = data.duration;
                                var durationRadios = document.querySelectorAll(
                                    'input[name="duration"]');
                                for (var i = 0; i < durationRadios.length; i++) {
                                    if (durationRadios[i].value === duration) {
                                        durationRadios[i].checked = true;
                                    }
                                }

                                if (data.has_discount) {
                                    $('#show_only_if_course_selected_has_discount').show();

                                    var hasDiscountCheckbox = $('#has_discount_checkbox');
                                    var discountAmount = data.discount;
                                    $('#show_discount_amount').text(discountAmount + ' Tshs');


                                } else {
                                    $('#show_only_if_course_selected_has_discount').hide();
                                }

                            },
                            error: function(xhr, textStatus, errorThrown) {
                                console.error('Error:', errorThrown);
                            }
                        });
                    } else {
                        feeInput.val('');
                    }
                });

                var start_date = $('#start_date');
                var end_date = $('#end_date');
                start_date.on('change', function() {
                    var selectedOption = $('option:selected', courseSelect);
                    var selectedCourseId = selectedOption.val();
                    if (selectedCourseId) {
                        var url = '{{ route('fees.get', ':id') }}';
                        url = url.replace(':id', selectedCourseId);
                        $.ajax({
                            url: url,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                var duration = data.duration;
                                var startDate = new Date(start_date.val());
                                var endDate = new Date(end_date.val());
                                var endDate = new Date(startDate);
                                endDate.setDate(endDate.getDate() + (duration * 7));
                                end_date.val(endDate.toISOString().slice(0, 10));
                            },
                            error: function(xhr, textStatus, errorThrown) {
                                console.error('Error:', errorThrown);
                            }
                        });
                    }
                });

            });
        </script>

        <script>
            $(document).ready(function() {
                var installmentRadios = $('.installment_type_class');
                var message = $('#if_partial_payment_is_checked_text').text('');
                var amountInput = $('#payment_amount');
                var feeInput = $('#fee');

                // default to full
                $('#installment_full').prop('checked', true);
                amountInput.prop('readonly', true);
                const staffCheckbox = $('#is_staff_student');
                function isBasicCourseSelected() {
                    var selectedText = $('#course_id option:selected').text() || '';
                    return selectedText.trim().toLowerCase() === 'basic computer application';
                }

                function computeAmountFor(type) {
                    var fee = parseFloat(feeInput.val() || 0);
                    if (staffCheckbox.is(':checked') && isBasicCourseSelected()) {
                        return (fee * 0.5).toFixed(2);
                    }
                    if (type === 'quarter') {
                        return (fee * 0.25).toFixed(2);
                    }
                    if (type === 'half') {
                        return (fee * 0.5).toFixed(2);
                    }
                    if (type === 'full') {
                        return fee.toFixed(2);
                    }
                    return '';
                }

                installmentRadios.on('change', function() {
                    var selected = installmentRadios.filter(':checked').val();
                    amountInput.prop('readonly', true);
                    amountInput.val(computeAmountFor(selected));
                    if (selected === 'quarter') {
                        message.text('Amount will be set to 25% of the fee');
                    } else if (selected === 'half') {
                        message.text('Amount will be set to 50% of the fee');
                    } else {
                        message.text('');
                    }
                });

                // also recompute on fee change (course selection affects fee)
                $('#course_id').on('change', function(){
                    var selected = installmentRadios.filter(':checked').val();
                    amountInput.val(computeAmountFor(selected));
                });

                // Staff student behavior: force full installment and lock radios
                function applyStaffStudentBehavior() {
                    if (staffCheckbox.is(':checked') && isBasicCourseSelected()) {
                        $('#installment_quarter, #installment_half').prop('disabled', true).prop('checked', false);
                        $('#installment_full').prop('checked', true);
                        amountInput.val(computeAmountFor('full'));
                        message.text('Staff Student: Amount is 50% of Course Fee. Payment will be considered Full.');
                    } else {
                        $('#installment_quarter, #installment_half').prop('disabled', false);
                        var selected = installmentRadios.filter(':checked').val();
                        amountInput.val(computeAmountFor(selected));
                        message.text('');
                    }
                }

                applyStaffStudentBehavior();
                staffCheckbox.on('change', function(){
                    applyStaffStudentBehavior();
                });

                // when course changes, re-apply staff behavior and recompute
                $('#course_id').on('change', function(){
                    applyStaffStudentBehavior();
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                updateDiscountFields();

                $('.apply_discount').change(function() {

                    updateDiscountFields();
                });

                function updateDiscountFields() {
                    const applyDiscountRadio = $('#discount_yes');
                    const ifDiscountRow = $('#if_discount_checked_row');
                    const discountReasonInputs = $('input[name="discount_reason"]');

                    if (applyDiscountRadio.filter(':checked').val() === '1') {
                        ifDiscountRow.show();
                        discountReasonInputs.first().prop('checked', true);
                    } else {
                        ifDiscountRow.hide();
                        discountReasonInputs.prop('checked', false);
                    }
                }
            });
        </script>
    @endpush
