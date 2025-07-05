<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::where('recipient_id', Auth::id())
            ->orWhere('sender_id', Auth::id())
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('messages.index', compact('messages'));
    }

    public function create()
    {
        $users = User::where('id', '!=', Auth::id())
            ->where('is_active', true)
            ->get(['id', 'name', 'email']);

        return view('messages.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $request->recipient_id,
            'subject' => $request->subject,
            'body' => $request->body,
        ]);

        return redirect()->route('messages.index')
            ->with('success', 'Message envoyé avec succès.');
    }

    public function show(Message $message)
    {
        // Vérifier que l'utilisateur peut voir ce message
        if ($message->recipient_id !== Auth::id() && $message->sender_id !== Auth::id()) {
            abort(403, 'Accès non autorisé à ce message.');
        }

        // Marquer comme lu si c'est le destinataire
        if ($message->recipient_id === Auth::id() && !$message->is_read) {
            $message->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return view('messages.show', compact('message'));
    }

    public function destroy(Message $message)
    {
        // Vérifier que l'utilisateur peut supprimer ce message
        if ($message->recipient_id !== Auth::id() && $message->sender_id !== Auth::id()) {
            abort(403, 'Accès non autorisé à ce message.');
        }

        $message->delete();

        return redirect()->route('messages.index')
            ->with('success', 'Message supprimé avec succès.');
    }
}