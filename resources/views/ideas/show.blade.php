<x-layout>
    <div class="py-8 max-w-4xl mx-auto">

        <div class="flex justify-between items-center">
            <a class="flex items-center gap-x-2 text-sm font-medium mb-2" href="{{ route('ideas.index') }}">
                <x-icons.arrow-back/>

                <span>Back to Ideas</span>
            </a>

            <div class="gap-x-3 flex items-center">
                <button class="btn btn-outlined">
                    <x-icons.external/>
                    Edit
                </button>
                <form action="{{ route('ideas.destroy', $idea) }}" method="POST">
                    @csrf
                    @method('delete')
                    <button class="btn btn-outlined text-red-500">
                        <x-icons.trash/>
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-6">
            <h1 class="font-bold text-4xl">{{ $idea->title }}</h1>

            <div class="mt-3 flex gap-x-3 items-center">
                <x-idea.status-label :status="$idea->status->value">{{ $idea->status->label() }}</x-idea.status-label>
                <div class="text-muted-foreground text-sm">{{ $idea->created_at->diffForHumans() }}</div>
            </div>

            <x-card class="mt-6">
                <div class="text-foreground max-w-none cursor-pointer">{{ $idea->description }}</div>
            </x-card>

            @if($idea->links->count())
                <div>
                    <h3 class="font-bold text-xl mt-6">Links</h3>
                    <div class="mt-3 space-y-2">
                        @foreach($idea->links as $link)
                            <x-card class="flex gap-x-2 items-center text-primary" href="{{ $link }}" target="_blank">
                                <x-icons.external/>
                                {{ $link }}
                            </x-card>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layout>
