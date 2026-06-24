<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $votes = $bill->votes()->with('user')->latest()->paginate(20);
        return view('votes.index', compact('bill', 'votes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Vote::class);
        $user = Auth::user();

        $validated = $request->validate([
            'vote' => 'required|in:for,against',
        ]);

        // Проверяем, что законопроект находится в стадии открытого голосования
        if (!$bill->isVotingActive()) {
            return back()->withErrors(['msg' => 'Голосование по этому законопроекту не активно.']);
        }

        $existingVote = Vote::where('bill_id', $bill->id)
                            ->where('user_id', $user->id)
                            ->exists();
        if ($existingVote) {
            return back()->withErrors(['msg' => 'Вы уже проголосовали за этот законопроект.']);
        }

        // Создаём голос
        Vote::create([
            'bill_id' => $bill->id,
            'user_id' => $user->id,
            'vote'    => $validated['vote'],
        ]);

        // Обновляем статус законопроекта (если нужно автоматически подводить итоги)
        // $bill->determineStatusByVotes(); // по желанию

        return redirect()->route('bills.show', $bill)
                         ->with('success', 'Ваш голос учтён.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Vote $vote)
    {
        $vote->load('user', 'bill');
        return view('votes.show', compact('vote'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vote $vote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vote $vote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vote $vote)
    {
        //
    }
}
