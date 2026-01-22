@php $editing = isset($payment) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12 col-lg-4">
        <x-inputs.select name="applicant_id" label="Applicant" class="select2" id="applicant_id" required>
            @php $selected = old('applicant_id', ($editing ? $payment->applicant_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Applicant</option>
            @foreach ($applicants as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-4">
        <x-inputs.select name="course_id" label="Course" required id="course_id">
            @php $selected = old('course_id', ($editing ? $payment->course_id : '')) @endphp
            {{-- <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Course</option>
            @foreach ($courses as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach --}}


        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-4">
        <x-inputs.number name="amount" id="amount" label="Amount" :value="old('amount', $editing ? $payment->amount : '')" min="0" max_length="25" placeholder="Amount" required readonly></x-inputs.number>
        <small id="installment_hint" class="text-danger"></small>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-4 mt-4">
        <h5>Installment</h5>
        <div class="d-flex">
            <div class="me-3">
                <x-inputs.radio id="installment_quarter" class="installment_type" name="installment_type" label="Quarter (25%)" value="quarter" :add-hidden-value="false" />
            </div>
            <div class="me-3">
                <x-inputs.radio id="installment_half" class="installment_type" name="installment_type" label="Half (50%)" value="half" :add-hidden-value="false" />
            </div>
            <div class="me-3">
                <x-inputs.radio id="installment_full" class="installment_type" name="installment_type" label="Full (100%)" value="full" :add-hidden-value="false" />
            </div>
        </div>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-4">
        <x-inputs.datetime name="date" label="Date"
            value="{{ old('date', $editing ? optional($payment->date)->format('Y-m-d\TH:i:s') : date('Y-m-d\TH:i:s')) }}"
            required></x-inputs.datetime>
    </x-inputs.group>
    <x-inputs.group class="col-sm-12 col-lg-4">
        <x-inputs.text name="receipt_number" label="Receipt Number *" :value="old('receipt_number', $editing ? ($payment->receipt_number ?? '') : '')" maxlength="255" placeholder="e.g. OUT-0123" required></x-inputs.text>
    </x-inputs.group>
</div>

@push('js')
<script>
    $(document).ready(function(){
        const applicant = $('#applicant_id');
        const course = $('#course_id');
        const amount = $('#amount');
        const hint = $('#installment_hint');
        let currentBalance = 0;

        function fetchBalance(){
            const appId = applicant.val();
            const courseId = course.val();
            if(!appId || !courseId){
                currentBalance = 0; amount.val(''); return;
            }
            $.ajax({
                url: '{{ route('payment.course_amount') }}'.replace('payment.course_amount','payment.course_amount'),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    student_id: appId,
                    course: courseId
                },
                success: function(data){
                    const val = parseFloat(data || 0);
                    currentBalance = isNaN(val) ? 0 : val;
                    computeFromInstallment();
                },
                error: function(){ currentBalance = 0; }
            });
        }

        function computeFromInstallment(){
            const selected = $(".installment_type:checked").val();
            amount.prop('readonly', true);
            if(selected === 'quarter'){
                amount.val((currentBalance * 0.25).toFixed(2));
                hint.text('Quarter of remaining balance');
            } else if(selected === 'half'){
                amount.val((currentBalance * 0.5).toFixed(2));
                hint.text('Half of remaining balance');
            } else if(selected === 'full'){
                amount.val((currentBalance).toFixed(2));
                hint.text('Full remaining balance');
            } else {
                amount.val('');
                hint.text('');
            }
        }

        applicant.on('change', fetchBalance);
        course.on('change', fetchBalance);
        $(document).on('change', '.installment_type', computeFromInstallment);

        // default selection
        $('#installment_full').prop('checked', true);
        computeFromInstallment();
    });
</script>
@endpush
