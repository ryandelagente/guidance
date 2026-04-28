<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Inbox — list of conversations.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Conversation::with(['counselor', 'studentUser', 'lastMessage'])
            ->where(function ($q) use ($user) {
                $q->where('counselor_id', $user->id)
                  ->orWhere('student_user_id', $user->id);
            })
            ->orderByDesc('last_message_at');

        $conversations = $query->paginate(30);

        // Pre-compute unread counts
        $conversations->getCollection()->transform(function ($c) use ($user) {
            $c->setAttribute('unread_count', $c->unreadCountFor($user));
            return $c;
        });

        // For staff: build the "Start new conversation" student picker
        $startableStudents = collect();
        if ($user->isStaff()) {
            $startableStudents = StudentProfile::with('user')
                ->whereHas('user')
                ->orderBy('last_name')
                ->limit(500)
                ->get(['id','first_name','last_name','student_id_number','user_id']);
        }

        return view('messages.index', compact('conversations', 'startableStudents'));
    }

    public function show(Conversation $conversation, Request $request)
    {
        $user = $request->user();
        abort_unless($conversation->involves($user), 403);

        $conversation->load(['counselor', 'studentUser']);

        $messages = $conversation->messages()->with('sender')->get();

        // Mark messages from the other person as read
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.show', compact('conversation', 'messages'));
    }

    /**
     * Start a new conversation (staff initiates with a student).
     */
    public function store(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isStaff(), 403);

        $data = $request->validate([
            'student_user_id' => 'required|exists:users,id',
            'subject'         => 'nullable|string|max:200',
            'body'            => 'required|string|max:5000',
        ]);

        // Find or create conversation
        $conversation = Conversation::firstOrCreate(
            [
                'counselor_id'    => $user->id,
                'student_user_id' => $data['student_user_id'],
            ],
            [
                'subject'         => $data['subject'] ?? 'Conversation',
                'last_message_at' => now(),
            ]
        );

        if (!empty($data['subject']) && !$conversation->subject) {
            $conversation->subject = $data['subject'];
        }
        $conversation->last_message_at = now();
        $conversation->save();

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'body'            => $data['body'],
        ]);

        return redirect()->route('messages.show', $conversation);
    }

    /**
     * Reply within an existing conversation.
     */
    public function reply(Conversation $conversation, Request $request)
    {
        $user = $request->user();
        abort_unless($conversation->involves($user), 403);

        $data = $request->validate(['body' => 'required|string|max:5000']);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'body'            => $data['body'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        return redirect()->route('messages.show', $conversation);
    }
}
