<x-layout>
    <x-form title="Edit your account" description="Need to make a tweet?">
        <form action="{{ route('profile.update') }}" method="POST" class="mt-10 space-y-4">
            @csrf
            @method('PATCH')

            <x-form.field name="name" label="Name" :value="$user->name" />
            <x-form.field name="email" type="email" label="Email" :value="$user->email" />
            <x-form.field name="password" type="password" label="New Password" />

            <button type="submit" class="btn mt-2 h-10 w-full">Edit</button>
        </form>
    </x-form>
</x-layout>
