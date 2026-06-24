<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Профиль') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900">Ваша роль</h3>

                    @php
                        $roleMap = [
                            'voter'    => 'Голосующий',
                            'proposer' => 'Предлагающий',
                            'both'     => 'Разработчик (голосующий и предлагающий)',
                        ];
                    @endphp

                    <p class="mt-1 text-sm text-gray-600">
                        {{ $roleMap[auth()->user()->role] ?? 'Неизвестно' }}
                    </p>

                    @if(auth()->user()->role === 'voter')
                        <p class="mt-2 text-xs text-gray-500">
                            Вы можете голосовать за законопроекты.
                            @if($proposerPercent < 10)
                                <br>Сейчас предлагающих: <strong>{{ $proposerPercent }}%</strong> (максимум 10%).
                                Вы можете стать предлагающим.
                            @else
                                <br>К сожалению, количество предлагающих уже достигло 10% от всех пользователей.
                            @endif
                        </p>

                        @if($proposerPercent < 10)
                            <form method="POST" action="{{ route('profile.role') }}" class="mt-3">
                                @csrf
                                <input type="hidden" name="role" value="proposer">
                                <x-primary-button>
                                    {{ __('Стать предлагающим') }}
                                </x-primary-button>
                            </form>
                        @endif
                    @endif

                    @if(auth()->user()->role === 'proposer')
                        <p class="mt-2 text-xs text-gray-500">
                            Вы можете предлагать новые законопроекты.
                            <br>Вы всегда можете стать голосующим.
                        </p>
                        <form method="POST" action="{{ route('profile.role') }}" class="mt-3">
                            @csrf
                            <input type="hidden" name="role" value="voter">
                            <x-secondary-button type="submit">
                                {{ __('Стать голосующим') }}
                            </x-secondary-button>
                        </form>
                    @endif

                    @if(auth()->user()->role === 'both')
                        <p class="mt-2 text-xs text-gray-500">
                            Вы разработчик — можете и голосовать, и предлагать.
                        </p>
                    @endif

                    @if ($errors->has('role'))
                        <p class="mt-2 text-sm text-red-600">{{ $errors->first('role') }}</p>
                    @endif
                    @if (session('status'))
                        <p class="mt-2 text-sm text-green-600">{{ session('status') }}</p>
                    @endif
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
