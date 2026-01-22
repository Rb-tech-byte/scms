@php $editing = isset($course) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12 col-lg-6 mb-3">
        <x-inputs.text name="title" label="Course Title *" :value="old('title', $editing ? $course->title : '')" maxlength="255" placeholder="Enter course title"
            required></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-6 mb-3">
        <x-inputs.number name="fee" label="Course  Fee *" :value="old('fee', $editing ? $course->fee : '')" placeholder="Fee"
            required></x-inputs.number>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-4">
        <x-inputs.select name="duration" label="Duration">
            @php $selected = old('duration', ($editing ? $course->duration : '')) @endphp
            <option value="1" {{ $selected == '1' ? 'selected' : '' }}>1 Week</option>
            <option value="2" {{ $selected == '2' ? 'selected' : '' }}>2 Week</option>
            <option value="3" {{ $selected == '3' ? 'selected' : '' }}>3 Week</option>
            <option value="4" {{ $selected == '4' ? 'selected' : '' }}>4 Week</option>
            <option value="5" {{ $selected == '5' ? 'selected' : '' }}>5 Week</option>
            <option value="6" {{ $selected == '6' ? 'selected' : '' }}>6 Week</option>
            <option value="7" {{ $selected == '7' ? 'selected' : '' }}>7 Week</option>
            <option value="7" {{ $selected == '7' ? 'selected' : '' }}>7 Week</option>
            <option value="8" {{ $selected == '8' ? 'selected' : '' }}>8 Week</option>
            <option value="9" {{ $selected == '9' ? 'selected' : '' }}>9 Week</option>
            <option value="10" {{ $selected == '10' ? 'selected' : '' }}>10 Week</option>
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-4">
        <x-inputs.select name="has_discount" label="Has Discount">
            @php $selected = old('has_discount', ($editing ? $course->has_discount : '')) @endphp
            <option value="0" {{ $selected == '0' ? 'selected' : '' }}>No</option>
            <option value="1" {{ $selected == '1' ? 'selected' : '' }}>Yes</option>
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12 col-lg-4 discount_amount none">
        <x-inputs.number name="discount" label="Discount Amount" :value="old('discount', $editing ? $course->discount : '')" placeholder="Discount"
            required></x-inputs.number>
    </x-inputs.group>
</div>


@push('js')

<script>
    // SHOW / HIDE DISCOUNT FIELD BASED ON SELECTED VALUE
    $(document).ready(function () {
        toggleDiscountField();
        $('select[name="has_discount"]').change(function () {
            toggleDiscountField();
        });
        function toggleDiscountField() {
            const hasDiscountSelect = $('select[name="has_discount"]');
            const discountField = $('input[name="discount"]').closest('.form-group'); // Assuming a Bootstrap-like structure

            if (hasDiscountSelect.val() === '1') {
                discountField.show();
                $('input[name="discount"]').prop('required', true);
            } else {
                discountField.hide();
                $('input[name="discount"]').val('');
                $('input[name="discount"]').prop('required', false);
            }
        }
    });
</script>

@endpush
