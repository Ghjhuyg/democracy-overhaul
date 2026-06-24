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
                                    <!-- Здесь ваши фильтры (без изменений) -->
                                    ...
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