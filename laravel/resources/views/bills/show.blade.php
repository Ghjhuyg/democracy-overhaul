<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $bill->title }}
        </h2>
    </x-slot>

    <div class="py-12" 
         x-data="billDetail({{ $bill->id }}, {{ $votes_for ?? 0 }}, {{ $votes_against ?? 0 }}, {{ json_encode($votes_list ?? []) }})"
         x-init="init()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('bills.index') }}" class="text-indigo-600 hover:underline">&larr; Назад к списку</a>
                    </div>

                    <div class="mb-6">
                        <h1 class="text-2xl font-bold">{{ $bill->title }}</h1>
                        <p class="text-sm text-gray-500 mt-1">
                            Автор: {{ $bill->user->name ?? 'Неизвестен' }}
                        </p>
                        <div class="mt-2 flex items-center gap-4">
                            <span class="text-xs px-2 py-1 rounded 
                                @if($bill->status == 'open') bg-blue-100 text-blue-800
                                @elseif($bill->status == 'accepted') bg-green-100 text-green-800
                                @elseif($bill->status == 'standby') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $statusMap[$bill->status] ?? $bill->status }}
                            </span>
                            <span class="text-xs text-gray-400">
                                Голосование: 
                                {{ $bill->voting_start_at ? $bill->voting_start_at->format('d.m.Y H:i') : '—' }}
                                – 
                                {{ $bill->voting_end_at ? $bill->voting_end_at->format('d.m.Y H:i') : '—' }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-700">Описание</h3>
                        <p class="mt-2 text-gray-700">{{ $bill->description }}</p>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-700">Голосование</h3>

                        @if($bill->status === 'open')
                            @php
                                $userVote = $bill->votes->where('user_id', auth()->id())->first();
                            @endphp

                            @if(in_array(auth()->user()->role, ['voter', 'both']))
                                @if(!$userVote)
                                    <div class="mt-3 flex gap-4">
                                        <form method="POST" action="{{ route('bills.vote', $bill) }}" 
                                              onsubmit="return confirm('Вы действительно хотите проголосовать ЗА этот законопроект?');">
                                            @csrf
                                            <input type="hidden" name="vote" value="for">
                                            <button type="submit" class="px-6 py-2 font-semibold bg-gray-100 rounded-md hover:text-green-700">
                                                За
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('bills.vote', $bill) }}" 
                                              onsubmit="return confirm('Вы действительно хотите проголосовать ПРОТИВ этого законопроекта?');">
                                            @csrf
                                            <input type="hidden" name="vote" value="against">
                                            <button type="submit" class="px-6 py-2 font-semibold bg-gray-100 rounded-md hover:text-red-700">
                                                Против
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <p class="mt-3 text-gray-600">
                                        Вы уже проголосовали: <strong>{{ $userVote->vote === 'for' ? 'ЗА' : 'ПРОТИВ' }}</strong>
                                    </p>
                                @endif
                            @else
                                <p class="mt-3 text-gray-500">Только голосующие могут участвовать в голосовании.</p>
                            @endif
                        @elseif($bill->status === 'standby')
                            <p class="mt-3 text-yellow-600">
                                Голосование ещё не началось. Оно начнётся 
                                {{ $bill->voting_start_at ? $bill->voting_start_at->format('d.m.Y H:i') : 'в ближайшее время' }}.
                            </p>

                        @elseif($bill->status === 'accepted')
                            <p class="mt-3 text-green-600 font-semibold">Законопроект принят!</p>

                        @elseif($bill->status === 'rejected')
                            <p class="mt-3 text-red-600 font-semibold">Законопроект отклонён.</p>    
                        @else
                            <p class="mt-3 text-gray-500">Голосование завершено.</p>
                        @endif
                    </div>

                    <!-- Результаты голосования (обновляемые) -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-700">Результаты голосования</h3>
                        <div class="mt-2 flex gap-6">
                            <div>
                                <span class="font-semibold text-green-600">За:</span> 
                                <span x-text="votesFor"></span>
                            </div>
                            <div>
                                <span class="font-semibold text-red-600">Против:</span> 
                                <span x-text="votesAgainst"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Список проголосовавших (обновляемый) -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-700">Список проголосовавших</h3>
                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-semibold text-green-600">За</h4>
                                <ul class="list-disc list-inside text-sm text-gray-700">
                                    <template x-for="vote in votesForList" :key="vote.user_name + vote.created_at">
                                        <li x-text="vote.user_name + ' (' + vote.created_at + ')'"></li>
                                    </template>
                                    <li x-show="votesForList.length === 0" class="text-gray-500">Нет голосов</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-semibold text-red-600">Против</h4>
                                <ul class="list-disc list-inside text-sm text-gray-700">
                                    <template x-for="vote in votesAgainstList" :key="vote.user_name + vote.created_at">
                                        <li x-text="vote.user_name + ' (' + vote.created_at + ')'"></li>
                                    </template>
                                    <li x-show="votesAgainstList.length === 0" class="text-gray-500">Нет голосов</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function billDetail(billId, initialFor, initialAgainst, initialVotes) {
        return {
            billId: billId,
            votesFor: initialFor,
            votesAgainst: initialAgainst,
            votesForList: initialVotes.filter(v => v.vote === 'for'),
            votesAgainstList: initialVotes.filter(v => v.vote === 'against'),
            ws: null,

            init() {
                // Подключаем WebSocket только один раз для всей страницы
                if (!window.__ws) {
                    window.__ws = new WebSocket('wss://api.democracy-overhaul.bagaev.ai-info.ru/ws');
                    window.__ws.onopen = () => console.log('WebSocket connected (detail)');

                    // Обработчик сообщений – общий для всех компонентов
                    window.__ws.onmessage = (event) => {
                        try {
                            const data = JSON.parse(event.data);
                            console.log('WebSocket message received:', data);

                            // Обрабатываем только голоса для этого законопроекта
                            if (data.type === 'vote' && data.bill_id === this.billId) {
                                this.handleVote(data);
                            }
                        } catch (e) {
                            console.error('Failed to parse WebSocket message:', e);
                        }
                    };

                    window.__ws.onclose = () => {
                        console.log('WebSocket disconnected');
                        window.__ws = null;
                    };
                }

                // Сохраняем ссылку на глобальное соединение в локальной переменной
                this.ws = window.__ws;
            },

            handleVote(data) {
                // Проверка: есть ли уже голос от этого пользователя?
                const existing = [...this.votesForList, ...this.votesAgainstList]
                    .some(v => v.user_id === data.user_id);

                if (existing) {
                    console.log('Голос от пользователя', data.user_id, 'уже учтён');
                    return; // игнорируем
                }

                // Добавляем новый голос
                if (data.vote === 'for') {
                    this.votesFor++;
                    this.votesForList.push({
                        user_id: data.user_id,
                        user_name: data.user_name || 'Неизвестный',
                        vote: 'for',
                        created_at: data.created_at || new Date().toLocaleString()
                    });
                } else if (data.vote === 'against') {
                    this.votesAgainst++;
                    this.votesAgainstList.push({
                        user_id: data.user_id,
                        user_name: data.user_name || 'Неизвестный',
                        vote: 'against',
                        created_at: data.created_at || new Date().toLocaleString()
                    });
                }
            },

            destroy() {
                // Не закрываем глобальное соединение – оно может использоваться другими компонентами
            }
        };
    }
</script>
</x-app-layout>