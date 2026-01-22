@props([
    'name',
    'label',
    'value',
])

<x-inputs.basic type="file" :name="$name" label="{{ $label ?? ''}}" :value="$value ?? ''" :attributes="$attributes"></x-inputs.basic>
