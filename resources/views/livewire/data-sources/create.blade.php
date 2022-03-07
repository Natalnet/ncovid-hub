<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        New Data Source
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <form wire:submit.prevent="store" autocomplete="off">
                    <x-jet-validation-errors class="mb-4" />
                    <div>
                        <label for="source_name" class="block text-sm font-medium text-gray-700">Data Source Name</label>
                        <div class="mt-1">
                            <input type="text" name="source_name" id="source_name" wire:model="sourceName" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div>
                        <label for="source_source" class="block text-sm font-medium text-gray-700">Source</label>
                        <div class="mt-1">
                            <input type="text" name="source_source" id="source_source" wire:model="sourceSource" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div>
                        <label for="csv_file" class="block text-sm font-medium text-gray-700">CSV</label>
                        <div class="mt-1">
                            <input type="file" name="csv_file" id="csv_file" wire:model="csvFile">
                            <div wire:loading wire:target="csvFile">Uploading...</div>
                        </div>
                    </div>
{{--                    <div>--}}
{{--                        <label for="csv_url" class="block text-sm font-medium text-gray-700">CSV URL</label>--}}
{{--                        <div class="mt-1">--}}
{{--                            <input type="text" name="csv_url" id="csv_url" wire:model="csvUrl" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    <button type="submit" class="mt-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
