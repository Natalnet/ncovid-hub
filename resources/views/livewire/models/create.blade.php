<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Insert Model
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <form wire:submit.prevent="store" autocomplete="off">
                    <x-jet-validation-errors class="mb-4" />
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                        <div class="mt-1">
                            <select id="location" name="location" wire:model="location" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="BR">Brasil (BR)</option>
                                <option value="AC">Acre (AC)</option>
                                <option value="AL">Alagoas (AL)</option>
                                <option value="AM">Amazonas (AM)</option>
                                <option value="AP">Amapá (AP)</option>
                                <option value="BA">Bahia (BA)</option>
                                <option value="CE">Ceará (CE)</option>
                                <option value="DF">Distrito Federal (DF)</option>
                                <option value="ES">Espírito Santo (ES)</option>
                                <option value="GO">Goiás (GO)</option>
                                <option value="MA">Maranhão (MA)</option>
                                <option value="MG">Minas Gerais (MG)</option>
                                <option value="MS">Mato Grosso do Sul (MS)</option>
                                <option value="MT">Mato Grosso (MT)</option>
                                <option value="PA">Pará (PA)</option>
                                <option value="PB">Paraíba (PB)</option>
                                <option value="PE">Pernambuco (PE)</option>
                                <option value="PI">Piauí (PI)</option>
                                <option value="PR">Paraná (PR)</option>
                                <option value="RJ">Rio de Janeiro (RJ)</option>
                                <option value="RN">Rio Grande do Norte (RN)</option>
                                <option value="RO">Rondônia (RO)</option>
                                <option value="RR">Roraima (RR)</option>
                                <option value="RS">Rio Grande do Sul (RS)</option>
                                <option value="SC">Santa Catarina (SC)</option>
                                <option value="SE">Sergipe (SE)</option>
                                <option value="SP">São Paulo (SP)</option>
                                <option value="TO">Tocantins (TO)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <div class="mt-1">
                            <input type="text" name="description" id="description" wire:model="description" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700">Model File</label>
                        <div class="mt-1">
                            <input type="file" name="file" id="file" wire:model="file">
                            <div wire:loading wire:target="csvFile">Uploading...</div>
                        </div>
                    </div>
                    <button type="submit" class="mt-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Insert
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
