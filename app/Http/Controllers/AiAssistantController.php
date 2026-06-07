<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Services\AiAssistantService;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    public function __construct(private readonly AiAssistantService $ai) {}

    public function index()
    {
        $this->authorize('use-ai-assistant');

        $conversations = AiConversation::where('user_id', auth()->id())
            ->latest()
            ->limit(30)
            ->get();

        return view('ai-assistant.index', compact('conversations'));
    }

    public function chat(Request $request)
    {
        $this->authorize('use-ai-assistant');

        $request->validate(['message' => ['required', 'string', 'max:2000']]);

        // Pass last 5 for context
        $history = AiConversation::where('user_id', auth()->id())
            ->latest()
            ->limit(5)
            ->get();

        $response = $this->ai->chat($request->message, $history);

        $convo = AiConversation::create([
            'user_id'  => auth()->id(),
            'message'  => $request->message,
            'response' => $response,
        ]);

        return response()->json([
            'response' => $response,
            'id'       => $convo->id,
        ]);
    }

    public function clear(Request $request)
    {
        $this->authorize('use-ai-assistant');

        AiConversation::where('user_id', auth()->id())->delete();

        return response()->json(['success' => true]);
    }
}
