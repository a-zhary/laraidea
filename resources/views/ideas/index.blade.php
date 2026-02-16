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
                <a href="{{ route('ideas.index', ['status' => $status->value]) }}"
                   class="btn {{ request('status') === $status->value ? '' : 'btn-outlined' }}">
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

        <x-modal name="create-idea" title="New Idea">
            <form
                x-data="{
                    status: 'pending',
                    newLink: '',
                    links: []
                }"
                action="{{ route('ideas.store') }}"
                method="POST">
                @csrf

                <div class="space-y-6">
                    <x-form.field
                        name="title"
                        label="title"
                        placeholder="Enter title for your idea"
                        required
                        autofocus/>

                    <x-form.error name="title"/>

                    <div class="space-y-2">
                        <label for="status" class="label">Status</label>

                        <div class="flex gap-x-3">
                            @foreach($statuses as $status)
                                <button
                                    type="button"
                                    @click="status = @js($status->value)"
                                    class="btn flex-1 h-10"
                                    :class="{'btn-outlined': status !== @js($status->value) }"
                                >
                                    {{ $status->label() }}
                                </button>
                            @endforeach

                            <input type="hidden" name="status" :value="status" class="input">
                        </div>
                        <x-form.error name="status"/>
                    </div>

                    <x-form.field
                        label="description"
                        name="description"
                        type="textarea"
                        placeholder="Describe your idea..."
                    />

                    <div>
                        <fieldset class="space-y-3">
                            <legend class="label">Links</legend>


                            <template x-for="(link, index) in links" :key="link">
                                <div class="flex gap-x-2 items-center">
                                    <input name="links[]" x-model="link" class="input flex-1" disabled />

                                    <button
                                        type="button"
                                        @click="links.splice(index, 1)"
                                        aria-label="Remove link button"
                                        class="form-muted-icon"
                                    >
                                        <x-icons.trash/>
                                    </button>
                                </div>
                            </template>

                            <div class="flex gap-x-2 items-center">
                                <input
                                    x-model="newLink"
                                    type="url"
                                    id="new-link"
                                    placeholder="https://example.com"
                                    autocomplete="url"
                                    class="input flex-1"
                                    spellcheck="false"
                                >
                                <button
                                    type="button"
                                    @click="links.push(newLink); newLink=''"
                                    :disabled="newLink.trim().length === 0"
                                    aria-label="Add a new link button"
                                    class="form-muted-icon"
                                >
                                    <x-icons.plus/>
                                </button>
                            </div>
                        </fieldset>
                    </div>

                    <div class="flex justify-end gap-x-5">
                        <button @click="$dispatch('close-modal')" type="button">Cancel</button>
                        <button type="submit" class="btn">Create</button>
                    </div>

                </div>

            </form>
        </x-modal>
    </div>
</x-layout>
