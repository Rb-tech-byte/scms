@php
    $notyfOptions = [
        'dismissible' => true,
        'duration' => 5000,
        'position' => [
            'x' => 'right',
            'y' => 'top'
        ]
    ];
@endphp

@foreach (['success', 'error','warning'] as $type)
    @if (session()->has($type))
        <script>
            var notyf = new Notyf(@json($notyfOptions));
            notyf.{{ $type }}('{{ session($type) }}');
        </script>
    @endif
@endforeach



<script>
    $(document).ready(function() {
        $('.alert').alert()
    })
</script>
