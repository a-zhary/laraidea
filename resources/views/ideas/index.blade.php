<x-layout>

    <div>
        <header class="py-8 md:py-12">
            <h1 class="text-3xl font-bold">Ideas</h1>
            <p class="text-muted-foreground text-sm mt-2">Capture your thoughts. Make a plan.</p>

            <x-card
                x-data
                @click="$dispatch('open-modal', 'create-idea')"
                class="mt-10 cursor-pointer h-32 w-full text-left"
                is="button">
                <p>What's the idea?</p>
            </x-card>
        </header>

        <div>
            <a href="{{ route('ideas.index') }}" class="btn {{ request()->has('status') ? 'btn-outlined' : '' }}">
                All
                <span class="text-xs pl-3">{{ $statusCounts->get('all') }}</span>
            </a>
            @foreach($statuses as $status)
                <a href="{{ route('ideas.index', ['status' => $status->value]) }}" class="btn {{ request('status') === $status->value ? '' : 'btn-outlined' }}">
                    {{ $status->label() }}
                    <span class="text-xs pl-3">{{ $statusCounts->get($status->value) }}</span>
                </a>
            @endforeach
        </div>

        <div class="mt-10 text-muted-foreground">
            <div class="grid md:grid-cols-2 gap-6">
                @forelse($ideas as $idea)
                    <x-card href="{{ route('ideas.show', $idea) }}">
                        <h3 class="text-foreground text-lg">{{ $idea->title }}</h3>

                        <div class="mt-1">
                            <x-idea.status-label status="{{ $idea->status }}">
                                {{ $idea->status->label() }}
                            </x-idea.status-label>
                        </div>

                        <div class="mt-5 line-clamp-3">
                            {{ $idea->description }}
                        </div>
                        <div class="mt-4">{{ $idea->created_at->diffForHumans() }}</div>
                    </x-card>
                @empty
                    <x-card>No ideas at this time.</x-card>
                @endforelse
            </div>
        </div>

        <div
            x-data="{show: false, name: 'create-idea'}"
            x-show="show"
            @open-modal.window="if($event.detail === name) show = true;"
            @keydown.escape.window="show = false"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-4 -translate-x-4"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0 -translate-y-4 -translate-x-4"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs"
            x-cloak
            role="dialog">
            <x-card @click.away="show = false">
                <p>I am a modal</p>
            </x-card>
        </div>

    </div>
</x-layout>
