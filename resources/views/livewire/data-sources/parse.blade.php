<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Fields parsing for {{ $dataSource->name }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                @if(count($csvColumns))
                    <form wire:submit.prevent="addMapping">
                        <div>
                            <label for="original" class="block text-sm font-medium text-gray-700">Original</label>
                            <select id="original" name="original" wire:model="state.original" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option>Select...</option>
                                @foreach($csvColumns as $column)
                                    <option value="{{ $column }}">{{ $column }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="mapped" class="block text-sm font-medium text-gray-700">Mapped</label>
                            <select id="mapped" name="mapped" wire:model="state.mapped" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option>Select...</option>
                                @foreach($supportedColumns as $column)
                                    <option value="{{ $column }}">{{ $column }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit">Add mapping</button>
                    </form>

                    @foreach($columnMappings as $mapped => $original)
                        <p>{{ $original }} field mapped to {{ $mapped }}</p>
                    @endforeach

                    <button type="button" wire:click="fetchData">Fetch Data</button>

                @endif
            </div>
        </div>
    </div>
</div>
