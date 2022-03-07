<div>
    @foreach(\App\Models\DataSource::all() as $dataSource)
        <div class="flex justify-between">
            <p>{{ $dataSource->name }}</p>
            <button type="button" wire:click="refetch({{ $dataSource->id }})">Refetch</button>
        </div>
    @endforeach
</div>
