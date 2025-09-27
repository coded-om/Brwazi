<x-filament-widgets::widget>
    <x-filament::section heading="اختصارات سريعة">
        <div class="grid grid-cols-2 gap-3">
            @foreach($this->getActions() as $action)
                <x-filament::button :icon="$action->getIcon()" :href="$action->getUrl()" tag="a">
                    {{ $action->getLabel() }}
                </x-filament::button>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
