@php $editing = isset($invoice) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12 col-lg-4 mb-3">
        <x-inputs.select name="applicant_id" label="Applicant" required>
            @php $selected = old('applicant_id', ($editing ? $invoice->applicant_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Applicant</option>
            @foreach($applicants as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-4 mb-3">
        <x-inputs.select name="enrollment_id" label="Enrollment" required>
            @php $selected = old('enrollment_id', ($editing ? $invoice->enrollment_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Enrollment</option>
            @foreach($enrollments as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
    <x-inputs.group class="col-sm-12 col-lg-4 mb-3">
        <x-inputs.number
            name="balance"
            label="Balance"
            :value="old('balance', ($editing ? $invoice->balance : ''))"
            placeholder="balance"
            required
        ></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-4 mb-3">
        <x-inputs.number
            name="paid_amount"
            label="Paid Amount"
            :value="old('paid_amount', ($editing ? $invoice->paid_amount : ''))"
            placeholder="Amount"
            required
        ></x-inputs.number>
    </x-inputs.group>

    

    <x-inputs.group class="col-sm-12 col-lg-4 mb-3">
        <x-inputs.date
            name="date"
            label="Date"
            value="{{ old('date', ($editing ? optional($invoice->date)->format('Y-m-d') : '')) }}"
            max="255"
            required
        ></x-inputs.date>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-4 mb-3">
        <x-inputs.textarea
            name="description"
            label="Description"
            maxlength="255"
            >{{ old('description', ($editing ? $invoice->description : ''))
            }}</x-inputs.textarea
        >
    </x-inputs.group>

    {{-- <x-inputs.group class="col-sm-12 col-lg-4 mb-3">
        <x-inputs.select name="status" label="Status">
            @php $selected = old('status', ($editing ? $invoice->status : '0')) @endphp
            <option value="0" {{ $selected == '0' ? 'selected' : '' }} >0</option>
            <option value="1" {{ $selected == '1' ? 'selected' : '' }} >1</option>
            <option value="2" {{ $selected == '2' ? 'selected' : '' }} >2</option>
        </x-inputs.select>
    </x-inputs.group> --}}
</div>
