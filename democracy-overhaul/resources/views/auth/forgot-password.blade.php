<x-guest-layout>
    <style>
        /* Скрыть логотип Laravel */
        .shrink-0 {
            display: none !important;
        }

        body {
            background: #f8fafc;
        }

        .guest-layout-container {
            max-width: 480px;
            margin: 0 auto;
            text-align: center;
            animation: fadeInUp 0.6s ease-out;
            padding: 2rem 1rem;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .word {
            font-family: 'Bebas Neue', cursive;
            font-size: 3.5rem;
            line-height: 1.1;
            letter-spacing: 0.02em;
            transition: transform 0.2s ease;
        }

        .democracy {
            color: #1e293b;
        }

        .overhaul {
            color: #4f46e5;
            margin-top: -0.3rem;
        }

        .form-card {
            background: white;
            border-radius: 1.5rem;
            padding: 1.8rem;
            margin-top: 2rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            text-align: left;
            font-family: system-ui, -apple-system, sans-serif;
        }

        .form-card label {
            display: block;
            font-weight: 500;
            font-size: 0.9rem;
            color: #1e293b;
            margin-bottom: 0.3rem;
        }

        .form-card input[type="email"] {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            font-size: 0.95rem;
            transition: all 0.2s;
            background: #fefefe;
        }

        .form-card input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .info-text {
            font-size: 0.9rem;
            color: #475569;
            margin-bottom: 1.5rem;
            line-height: 1.4;
        }

        .action-buttons {
            display: flex;
            justify-content: center;   /* ← теперь кнопка всегда по центру */
            margin-top: 1.5rem;
        }

        /* Кнопка: компактная, без растяжения */
        .btn-primary-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            background-color: #4f46e5;
            color: white;
            white-space: nowrap;
            width: auto;
            min-width: unset;
        }

        /* На мобильных устройствах разрешаем перенос и оставляем центрирование */
        @media (max-width: 480px) {
            .btn-primary-custom {
                white-space: normal;
                font-size: 0.85rem;
                padding: 0.6rem 1rem;
                width: auto;
            }
            .action-buttons {
                justify-content: center;
            }
        }

        .btn-primary-custom:hover {
            transform: scale(1.02);
            letter-spacing: 0.03em;
            background-color: #4338ca;
        }

        .text-link {
            font-size: 0.85rem;
            color: #4f46e5;
            text-decoration: none;
            display: inline-block;
            margin-top: 1rem;
        }

        .text-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .word { font-size: 2.5rem; }
            .form-card { padding: 1.5rem; }
        }
    </style>

    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

    <div class="guest-layout-container">
        <div class="word democracy">DEMOCRACY</div>
        <div class="word overhaul">OVERHAUL</div>

        <div class="form-card">
            <!-- Session Status (успешная отправка) -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="info-text">
                Забыли пароль? Укажите ваш email, и мы вышлем ссылку для сброса пароля.
            </div>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="action-buttons">
                    <x-primary-button class="btn-primary-custom">
                        {{ __('Отправить ссылку для сброса') }}
                    </x-primary-button>
                </div>

                <div style="text-align: center; margin-top: 1rem;">
                    <a class="text-link" href="{{ route('login') }}">
                        {{ __('Вспомнили пароль? Войти') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>