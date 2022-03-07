<?php

namespace App\Http\Livewire\DataSources;

use App\Jobs\FetchData;
use App\Models\DataSource;
use Livewire\Component;

class Index extends Component
{
    public function refetch($sourceId)
    {
        FetchData::dispatch(DataSource::findOrFail($sourceId));
    }

    public function render()
    {
        return view('livewire.data-sources.index');
    }
}
