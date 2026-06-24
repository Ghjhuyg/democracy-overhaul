<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Законопроекты') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Верхняя панель -->
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-4">
                            <h3 class="text-lg font-medium">Список законопроектов</h3>
                            <span class="text-sm text-gray-500">{{ now()->format('H:i') }}</span>
                        </div>
                        @if(in_array(auth()->user()->role, ['proposer', 'both']))
                        <a href="{{ route('bills.create') }}" class="inline-flex items-center border border-transparent rounded-md text-lg font-medium tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Создать законопроект') }}
                        </a>
                        @endif
                    </div>

                    <!-- Две колонки -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Левая колонка – фильтры (сворачиваемые) -->
                        <div class="md:col-span-1" x-data="{ open: false }">
                            <button @click="open = !open" type="button" class="w-full flex items-center justify-between px-4 py-2 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                <span class="font-semibold text-gray-700">Фильтры</span>
                                <svg x-show="!open" class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                <svg x-show="open" class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                            </button>

                            <div x-show="open" x-collapse>
                                <form method="GET" action="{{ route('bills.index') }}" class="bg-gray-50 p-4 rounded-lg shadow-sm mt-2">
                                    <h4 class="font-semibold text-gray-700 mb-3">Фильтры</h4>
                                                                        <h4 class="font-semibold text-gray-700 mb-3">Фильтры</h4>

                                    <!-- Фильтр по статусу (чекбоксы) -->
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
                                        <div class="space-y-1">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="statuses[]" value="standby" 
                                                    @if(in_array('standby', (array)($filters['statuses'] ?? []))) checked @endif
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                <span class="ml-2 text-sm text-gray-700">Ожидают</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="statuses[]" value="open" 
                                                    @if(in_array('open', (array)($filters['statuses'] ?? []))) checked @endif
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                <span class="ml-2 text-sm text-gray-700">Открытые</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="statuses[]" value="accepted" 
                                                    @if(in_array('accepted', (array)($filters['statuses'] ?? []))) checked @endif
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                <span class="ml-2 text-sm text-gray-700">Принятые</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="statuses[]" value="rejected" 
                                                    @if(in_array('rejected', (array)($filters['statuses'] ?? []))) checked @endif
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                <span class="ml-2 text-sm text-gray-700">Отклонённые</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Даты начала -->
                                    <div class="mb-3">
                                        <label for="start_from" class="block text-sm font-medium text-gray-700 mt-2">Начало (с)</label>
                                        <input type="datetime-local" name="start_from" id="start_from" value="{{ $filters['start_from'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>

                                    <div class="mb-3">
                                        <label for="start_to" class="block text-sm font-medium text-gray-700 mt-2">Начало (по)</label>
                                        <input type="datetime-local" name="start_to" id="start_to" value="{{ $filters['start_to'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>

                                    <!-- Даты окончания -->
                                    <div class="mb-3">
                                        <label for="end_from" class="block text-sm font-medium text-gray-700 mt-2">Окончание (с)</label>
                                        <input type="datetime-local" name="end_from" id="end_from" value="{{ $filters['end_from'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>

                                    <div class="mb-3">
                                        <label for="end_to" class="block text-sm font-medium text-gray-700 mt-2">Окончание (по)</label>
                                        <input type="datetime-local" name="end_to" id="end_to" value="{{ $filters['end_to'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>

                                    <!-- Кнопки -->
                                    <div class="flex gap-2 mt-2">
                                        <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                            Применить
                                        </button>
                                        <a href="{{ route('bills.index') }}" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                                            Сбросить
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Правая колонка – список законопроектов -->
                        <div class="md:col-span-3">
                            <!-- Контейнер для React -->
                            <div id="react-root"></div>

                            <!-- Скрытый контейнер с серверным HTML для начального состояния -->
                            <div id="bills-list" style="display:none;">
                                @if($bills->count())
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @foreach($bills as $bill)
                                            <a href="{{ route('bills.show', $bill) }}" class="block border rounded-lg p-4 shadow hover:shadow-md transition bg-white">
                                                <h3 class="text-lg font-bold">{{ $bill->title }}</h3>
                                                <p class="text-sm text-gray-600 mt-1">
                                                    {{ Str::limit($bill->description, 120) }}
                                                </p>
                                                <div class="mt-3 flex justify-between items-center">
                                                    @php
                                                    $statusMap = [
                                                        'open'     => 'Открыто',
                                                        'accepted' => 'Принято',
                                                        'rejected' => 'Отклонено',
                                                        'standby'  => 'Ожидание',
                                                    ];
                                                    @endphp
                                                    <span class="text-xs px-2 py-1 rounded 
                                                        @if($bill->status == 'open') bg-blue-100 text-blue-800
                                                        @elseif($bill->status == 'accepted') bg-green-100 text-green-800
                                                        @elseif($bill->status == 'standby') bg-yellow-100 text-yellow-800
                                                        @else bg-red-100 text-red-800 @endif">
                                                        {{ $statusMap[$bill->status] ?? $bill->status }}
                                                    </span>
                                                    <span class="text-xs text-gray-500">
                                                        Автор: {{ $bill->user->name ?? 'Автор неизвестен' }}
                                                    </span>
                                                </div>
                                                <div class="mt-2 text-xs text-gray-400">
                                                    Голосование: 
                                                    {{ $bill->voting_start_at ? $bill->voting_start_at->format('d.m.Y H:i') : '—' }}
                                                    – 
                                                    {{ $bill->voting_end_at ? $bill->voting_end_at->format('d.m.Y H:i') : '—' }}
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                    <div class="mt-6">
                                        {{ $bills->links() }}
                                    </div>
                                @else
                                    <p class="text-gray-500 text-center py-4">Законопроектов пока нет.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>