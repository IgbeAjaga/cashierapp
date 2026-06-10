<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
<div class="mt-4">
    <x-input-label for="password" :value="__('Password')" />

    <div class="relative">
        <x-text-input id="password" class="block mt-1 w-full pr-10"
                      type="password"
                      name="password"
                      required autocomplete="new-password" />

        <button type="button"
                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700 toggle-password"
                data-target="password">

            <svg class="h-5 w-5 eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6.364 0c-1.73 4.039-5.523 7-9.364 7s-7.634-2.961-9.364-7c1.73-4.039 5.523-7 9.364-7s7.634 2.961 9.364 7z" />
            </svg>

            <svg class="h-5 w-5 eye-closed hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13.875 18.825A10.05 10.05 0 0112 19c-3.841 0-7.634-2.961-9.364-7a10.522 10.522 0 012.887-4.36m3.087-2.06A9.956 9.956 0 0112 5c3.841 0 7.634 2.961 9.364 7a10.522 10.522 0 01-4.293 5.066M15 12a3 3 0 00-4.243-2.829M9.88 9.88L4 4m5.88 5.88L14.12 14.12M20 20L14.12 14.12" />
            </svg>
        </button>
    </div>

    <x-input-error :messages="$errors->get('password')" class="mt-2" />
</div>

<!-- Confirm Password -->
<div class="mt-4">
    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

    <div class="relative">
        <x-text-input id="password_confirmation" class="block mt-1 w-full pr-10"
                      type="password"
                      name="password_confirmation"
                      required autocomplete="new-password" />

        <button type="button"
                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700 toggle-password"
                data-target="password_confirmation">

            <svg class="h-5 w-5 eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6.364 0c-1.73 4.039-5.523 7-9.364 7s-7.634-2.961-9.364-7c1.73-4.039 5.523-7 9.364-7s7.634 2.961 9.364 7z" />
            </svg>

            <svg class="h-5 w-5 eye-closed hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13.875 18.825A10.05 10.05 0 0112 19c-3.841 0-7.634-2.961-9.364-7a10.522 10.522 0 012.887-4.36m3.087-2.06A9.956 9.956 0 0112 5c3.841 0 7.634 2.961 9.364 7a10.522 10.522 0 01-4.293 5.066M15 12a3 3 0 00-4.243-2.829M9.88 9.88L4 4m5.88 5.88L14.12 14.12M20 20L14.12 14.12" />
            </svg>
        </button>
    </div>

    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
</div>
        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function () {

            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);

            const isPassword = input.getAttribute('type') === 'password';

            input.setAttribute('type', isPassword ? 'text' : 'password');

            const eyeOpen = this.querySelector('.eye-open');
            const eyeClosed = this.querySelector('.eye-closed');

            eyeOpen.classList.toggle('hidden');
            eyeClosed.classList.toggle('hidden');
        });
    });

});
</script>
