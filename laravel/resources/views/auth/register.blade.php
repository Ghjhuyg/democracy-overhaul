<x-guest-layout>
    <div class="guest-layout-container">
        <a href="{{ url('/') }}" style="text-decoration: none; display: inline-block;">
        <div class="word democracy">DEMOCRACY</div>
        <div class="word overhaul">OVERHAUL</div>
        </a>
        <div class="form-card">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Имя')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Пароль')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Подтвердите пароль')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="action-buttons">
                    <a class="text-link" href="{{ route('login') }}">
                        {{ __('Уже есть аккаунт?') }}
                    </a>
                    <button type="submit" class="btn-primary-custom">
                        {{ __('Регистрация по почте') }}
                    </button>
                </div>

                    <hr style="margin-top: 1.5rem; margin-bottom: 1.5rem;">

                        <a href="{{ route('auth.github') }}" class="btn-dark-reg">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12c0 4.42 2.87 8.17 6.84 9.49.5.09.68-.21.68-.48 0-.24-.01-.88-.01-1.73-2.78.6-3.37-1.2-3.37-1.2-.46-1.16-1.12-1.47-1.12-1.47-.91-.62.07-.61.07-.61 1.01.07 1.54 1.04 1.54 1.04.9 1.54 2.36 1.09 2.94.84.09-.65.35-1.09.64-1.34-2.24-.25-4.6-1.12-4.6-4.98 0-1.1.39-2 1.03-2.71-.1-.25-.45-1.27.1-2.65 0 0 .84-.27 2.75 1.02.8-.22 1.65-.33 2.5-.33.85 0 1.7.11 2.5.33 1.91-1.29 2.75-1.02 2.75-1.02.55 1.38.2 2.4.1 2.65.64.71 1.03 1.61 1.03 2.71 0 3.87-2.36 4.73-4.62 4.98.36.31.68.92.68 1.85 0 1.34-.01 2.42-.01 2.75 0 .27.18.58.69.48C19.13 20.17 22 16.42 22 12c0-5.52-4.48-10-10-10z"/>
                            </svg>
                            Регистрация через GitHub
                        </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>