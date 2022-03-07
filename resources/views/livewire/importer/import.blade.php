<div>
    <form wire:submit.prevent="import">
        <x-jet-validation-errors class="mb-4" />

        <div>
            <label for="csv_url" class="block text-sm font-medium text-gray-700">CSV URL</label>
            <div class="mt-1">
                <input type="text" name="csv_url" id="csv_url" wire:model="csvUrl" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="you@example.com">
            </div>
        </div>
        <button type="submit">Import</button>
    </form>

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
    @endif
</div>
