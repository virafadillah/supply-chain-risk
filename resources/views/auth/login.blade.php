<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-7">
        <h2 class="text-lg font-semibold text-gray-900">Masuk ke akun kamu</h2>
        <p class="text-sm text-gray-400 mt-1">Pantau risiko rantai pasok global kamu</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-600 text-sm font-medium" />
            <x-text-input id="email"
                class="block mt-1.5 w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm"
                type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-gray-600 text-sm font-medium" />
            <x-text-input id="password"
                class="block mt-1.5 w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm"
                type="password" name="password" required autocomplete="current-password"
                placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-5">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" name="remember">
                <span class="ms-2 text-sm text-gray-500">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-gray-400 hover:text-blue-600 transition-colors" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <button type="submit"
            class="w-full mt-6 py-2.5 rounded-lg font-medium text-white text-sm transition-colors"
            style="background:#378ADD;"
            onmouseover="this.style.background='#185FA5'" onmouseout="this.style.background='#378ADD'">
            {{ __('Log in') }}
        </button>
    </form>
</x-guest-layout>