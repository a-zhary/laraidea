<nav class="border-b border-border px-6">
    <div class="max-w-7xl mx-auto h-16 flex items-center justify-between">
        <div>
            <a href="/">Idea</a>
        </div>

        <div class="flex gap-x-5 items-center">
            @guest
                <a href="/login">Sign in</a>
                <a href="/register" class="btn">Register</a>
            @endguest

            @auth
                <form action="/logout" method="POST">
                    @csrf
                    <button class="btn btn-outlined">Logout</button>
                </form>
            @endauth

        </div>
    </div>
</nav>
