<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Vote; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\RedisEventHelper;
use Illuminate\Support\Facades\Log;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
    $query = Bill::with('user')->orderBy('created_at', 'desc');

    if ($request->filled('statuses')) {
        $statuses = (array) $request->input('statuses');
        $query->whereIn('status', $statuses);
    }

    if ($request->filled('start_from')) {
        $query->where('voting_start_at', '>=', $request->start_from);
    }
    if ($request->filled('start_to')) {
        $query->where('voting_start_at', '<=', $request->start_to);
    }
    if ($request->filled('end_from')) {
        $query->where('voting_end_at', '>=', $request->end_from);
    }
    if ($request->filled('end_to')) {
        $query->where('voting_end_at', '<=', $request->end_to);
    }

    $bills = $query->paginate(10);

    $filters = $request->only(['statuses', 'start_from', 'start_to', 'end_from', 'end_to']);

    return view('bills.index', compact('bills', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!in_array(Auth::user()->role, ['proposer', 'both'])) 
        {
            abort(403, 'У вас нет прав для создания законопроектов.');
        }
        return view('bills.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Проверка роли
        if (!in_array(Auth::user()->role, ['proposer', 'both'])) 
        {
            abort(403, 'У вас нет прав для создания законопроектов.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'voting_start_at' => 'nullable|date|after_or_equal:today',
            'voting_end_at' => 'nullable|date|after:voting_start_at',
        ]);

        $bill = Bill::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'standby',
            'voting_start_at' => $validated['voting_start_at'] ?? null,
            'voting_end_at' => $validated['voting_end_at'] ?? null,
        ]);

        RedisEventHelper::publish('bill_created', [
        'bill_id' => $bill->id,
        'title' => $bill->title,
        'description' => $bill->description,
        'status' => $bill->status,
        'user_name' => Auth::user()->name,
        'voting_start_at' => $bill->voting_start_at ? $bill->voting_start_at->toISOString() : null,
        'voting_end_at' => $bill->voting_end_at ? $bill->voting_end_at->toISOString() : null,
        'created_at' => $bill->created_at->toISOString(),
        ]);
        $payload = [
            'type' => 'bill_created',
            'bill_id' => $bill->id,
            'title' => $bill->title,
            'description' => $bill->description,
            'status' => $bill->status,
            'user_name' => auth()->user()->name,
            'voting_start_at' => $bill->voting_start_at ? $bill->voting_start_at->toISOString() : null,
            'voting_end_at' => $bill->voting_end_at ? $bill->voting_end_at->toISOString() : null,
            'created_at' => $bill->created_at->toISOString(),
        ];
        Log::info('Redis published', ['payload' => $payload]);
        return redirect()->route('bills.index')->with('success', 'Законопроект создан!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bill $bill)
    {
        $bill->load(['user', 'votes.user']);
        $statusMap = [
        'open'     => 'Открыто',
        'accepted' => 'Принято',
        'rejected' => 'Отклонено',
        'standby'  => 'Ожидание',
        ];
        $votes_for = $bill->votes->where('vote', 'for')->count();
        $votes_against = $bill->votes->where('vote', 'against')->count();

    // Список проголосовавших (для отображения в списке)
        $votes_list = $bill->votes->map(function ($vote) {
            return [
                'user_id'    => $vote->user_id,
                'user_name'  => $vote->user->name ?? 'Неизвестный',
                'vote'       => $vote->vote,
                'created_at' => $vote->created_at->format('d.m.Y H:i'),
            ];
            })->values()->toArray(); // чистый массив

    return view('bills.show', compact('bill', 'statusMap', 'votes_for', 'votes_against', 'votes_list'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bill $bill)
    {
        $this->authorize('update', $bill);
        return view('bills.edit', compact('bill'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bill $bill)
    {
        $this->authorize('update', $bill);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'voting_start_at' => 'nullable|date|after_or_equal:today',
            'voting_end_at' => 'nullable|date|after:voting_start_at',
        ]);

        $bill->update($validated);

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Законопроект обновлён.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bill $bill)
    {
        $this->authorize('delete', $bill);
        
        $bill->delete();
        
        return redirect()->route('bills.index')
            ->with('success', 'Законопроект удалён.');
    }

    public function vote(Request $request, Bill $bill)
    {
    if ($bill->status !== 'open') {
        return back()->withErrors(['vote' => 'Голосование по этому законопроекту уже завершено.']);
    }

    if (!in_array(auth()->user()->role, ['voter', 'both'])) {
        return back()->withErrors(['vote' => 'У вас нет права голосовать.']);
    }

    if ($bill->votes()->where('user_id', auth()->id())->exists()) {
        return back()->withErrors(['vote' => 'Вы уже проголосовали по этому законопроекту.']);
    }

    $request->validate([
        'vote' => 'required|in:for,against',
    ]);

    Vote::create([
        'bill_id' => $bill->id,
        'user_id' => auth()->id(),
        'vote' => $request->vote,
    ]);

    if ($bill->voting_end_at && now()->gte($bill->voting_end_at)) {
        $bill->determineStatusByVotes();
    }

    $user = auth()->user();
    RedisEventHelper::publish('vote', [
        'bill_id'    => $bill->id,
        'user_id'    => auth()->id(),
        'vote'       => $request->vote,
        'user_name'  => auth()->user()->name,
        'created_at' => now()->format('d.m.Y H:i'),
    ]); 

    return back()->with('success', 'Ваш голос учтён.');
    }
}
