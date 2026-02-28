<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <button class="btn btn-primary" type="submit">Enregistrer</button>
    </form>
</x-filament-panels::page>
