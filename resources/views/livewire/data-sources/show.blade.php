<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $dataSource->name }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                @if($dataSource->mappings)
                    @foreach($dataSource->mappings as $mapped => $original)
                        <p>{{ $original }} field mapped to {{ $mapped }}</p>
                    @endforeach
                @endif

                    <div id='myDiv'><!-- Plotly chart will be drawn inside this DIV --></div>
            </div>
        </div>
    </div>
</div>

@push('modals')
    <script>
        var trace1 = {
            x: @json($x),
            y: @json($y),
            mode: 'lines',
            line: {
                color: 'rgb(201,59,59)',
                width: 2
            },
            name: 'New Deaths'
        };
        var data = [trace1]

        Plotly.newPlot('myDiv', data);
    </script>
@endpush
