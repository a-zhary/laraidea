@props(['idea' => new \App\Models\Idea()])

<x-modal name="{{ $idea->exists ? 'edit-idea' : 'create-idea' }}"
         title="{{ $idea->exists ? 'Edit Idea' : 'Create Idea' }}">
    <form
        x-data="{
                    status: @js(old('status', $idea->status->value)),
                    newLink: '',
                    links: @js(old('links', $idea->links ?? [])),
                    newStep: '',
                    steps: @js(old('steps', $idea->steps->map->only(['id', 'description', 'completed'])))
                }"
        action="{{ $idea->exists ? route('ideas.update', $idea) : route('ideas.store') }}"
        method="POST"
        enctype="multipart/form-data">
        @csrf

        @if($idea->exists())
            @method('PATCH')
        @endif

        <div class="space-y-6">
            <x-form.field
                name="title"
                label="title"
                placeholder="Enter title for your idea"
                required
                :value="$idea->title"
                autofocus/>

            <x-form.error name="title"/>

            <div class="space-y-2">
                <label for="status" class="label">Status</label>

                <div class="flex gap-x-3">
                    @foreach(\App\IdeaStatus::cases() as $status)
                        <button
                            data-test="button-status-{{ $status->value }}"
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
                :value="$idea->description"
            />

            <div class="space-y-2">
                <label for="image" class="label">Featured Image</label>

                @if($idea->image_path)
                    <div class="space-y-2">
                        <img class="w-full h-48 object-cover rounded-lg"
                             src="{{ asset('storage/' . $idea->image_path) }}" alt="">
                        <button form="delete-image-form" class="btn btn-outlined h-10 w-full">Remove
                            image
                        </button>
                    </div>
                @endif

                <input type="file" name="image" accept="image">
                <x-form.error name="image"/>
            </div>

            <div>
                <fieldset class="space-y-2">
                    <legend class="label">Actionable Steps</legend>


                    <template x-for="(step, index) in steps" :key="step.id || index">

                        <div class="flex gap-x-2 items-center">
                            <input :name="`steps[${index}][description]`" x-model="step.description" class="input flex-1" readonly/>
                            <input type="hidden" :name="`steps[${index}][completed]`" x-model="step.completed ? '1' : '0'" class="input flex-1" readonly/>

                            <button
                                type="button"
                                @click="steps.splice(index, 1)"
                                aria-label="Remove link button"
                                class="form-muted-icon"
                            >
                                <x-icons.trash/>
                            </button>
                        </div>

                    </template>

                    <div class="flex gap-x-2 items-center">

                        <input
                            data-test="new-step"
                            x-model="newStep"
                            type="text"
                            id="new-step"
                            placeholder="What needs to be done"
                            class="input flex-1"
                            spellcheck="false"
                        >
                        <button
                            data-test="submit-new-step-button"
                            type="button"
                            @click="
                                steps.push({description: newStep.trim(), completed: false}); newStep=''
                            "
                            :disabled="newStep.trim().length === 0"
                            aria-label="Add a new step button"
                            class="form-muted-icon"
                        >
                            <x-icons.plus/>
                        </button>
                    </div>
                </fieldset>
            </div>


            <div>
                <fieldset class="space-y-2">
                    <legend class="label">Links</legend>


                    <template x-for="(link, index) in links" :key="link">
                        <div class="flex gap-x-2 items-center">
                            <input name="links[]" x-model="link" class="input flex-1" readonly/>

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
                            data-test="new-link"
                            x-model="newLink"
                            type="url"
                            id="new-link"
                            placeholder="https://example.com"
                            autocomplete="url"
                            class="input flex-1"
                            spellcheck="false"
                        >
                        <button
                            data-test="submit-new-link-button"
                            type="button"
                            @click="links.push(newLink.trim()); newLink=''"
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
                <button data-test="submit-idea-form" type="submit" class="btn">{{ $idea->exists() ? 'Update' : 'Create' }}</button>
            </div>

        </div>

    </form>

    @if($idea->image_path)
        <form method="POST" id="delete-image-form" action="{{ route('ideas.image.destroy', $idea) }}">
            @csrf
            @method('DELETE')
        </form>
    @endif
</x-modal>
