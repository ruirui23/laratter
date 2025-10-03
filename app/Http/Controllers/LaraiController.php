<?php

namespace App\Http\Controllers;

use App\Models\Larai;
use App\Services\GeminiService;
use Illuminate\Http\Request;

class LaraiController extends Controller
{
    private $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 親スレッド（parent_idがnull）のみを取得
        $conversations = Larai::with(['user', 'children'])
            ->where('user_id', auth()->id())
            ->whereNull('parent_id')
            ->latest()
            ->paginate(10);

        return view('larai.index', compact('conversations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('larai.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:larais,id'
        ]);

        // Gemini APIに質問を送信
        $result = $this->geminiService->generateResponse($request->question);

        // データベースに保存
        $larai = Larai::create([
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'question' => $request->question,
            'response' => $result['response'],
            'metadata' => $result['metadata'] ?? null
        ]);

        // 親がある場合は親を、ない場合は自分自身を表示
        $displayId = $request->parent_id ?? $larai->id;

        if ($result['success']) {
            return redirect()->route('larai.show', $displayId)
                ->with('success', 'Laraiが回答しました！');
        } else {
            return redirect()->route('larai.show', $displayId)
                ->with('error', 'エラーが発生しましたが、回答は保存されました。');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Larai $larai)
    {
        // 自分の質問のみ表示
        if ($larai->user_id !== auth()->id()) {
            abort(403, 'この会話を表示する権限がありません。');
        }

        // 会話スレッド全体を取得
        $thread = $larai->getThread();

        // ルート（最初の質問）を取得
        $root = $larai->getRoot();

        return view('larai.show', compact('larai', 'thread', 'root'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Larai $larai)
    {
        // 自分の会話のみ削除可能
        if ($larai->user_id !== auth()->id()) {
            abort(403, 'この会話を削除する権限がありません。');
        }

        // 会話のルート（親）を取得
        $root = $larai->getRoot();

        // ルートを削除（cascadeで子も削除される）
        $root->delete();

        return redirect()->route('larai.index')
            ->with('success', '会話スレッドを削除しました。');
    }
}
