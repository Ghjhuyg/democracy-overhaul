<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Создать законопроект') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('bills.store') }}" autocomplete="off">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Название')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Описание')" />
                            <textarea id="description" name="description" rows="5" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="voting_start_at" :value="__('Дата начала голосования')" />
                            <x-text-input id="voting_start_at" class="block mt-1 w-full" type="datetime-local" name="voting_start_at" :value="old('voting_start_at')" />
                            <x-input-error :messages="$errors->get('voting_start_at')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="voting_end_at" :value="__('Дата окончания голосования')" />
                            <x-text-input id="voting_end_at" class="block mt-1 w-full" type="datetime-local" name="voting_end_at" :value="old('voting_end_at')" />
                            <x-input-error :messages="$errors->get('voting_end_at')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Создать') }}</x-primary-button>
                            <a href="{{ route('bills.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Отмена</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>