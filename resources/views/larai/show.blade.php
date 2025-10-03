<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laraiとの会話') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- 成功・エラーメッセージ -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- 会話ヘッダー -->
                    <div class="mb-6 pb-4 border-b dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold mb-1">会話スレッド</h3>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    開始: {{ $root->created_at->format('Y年m月d日 H:i') }}
                                </div>
                            </div>
                            @if($root->metadata && isset($root->metadata['model']))
                                <div class="text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded">
                                    {{ $root->metadata['model'] }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 会話スレッド全体を表示 -->
                    <div class="space-y-6">
                        @foreach($thread as $index => $message)
                            <!-- 質問部分 -->
                            <div class="mb-4">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                                            <div class="flex justify-between items-center mb-2">
                                                <div class="font-bold text-blue-800 dark:text-blue-200 text-sm">
                                                    あなたの質問 @if($index > 0) #{{ $index + 1 }} @endif
                                                </div>
                                                <div class="text-xs text-blue-600 dark:text-blue-400">
                                                    {{ $message->created_at->format('H:i') }}
                                                </div>
                                            </div>
                                            <div class="text-blue-700 dark:text-blue-300 whitespace-pre-wrap">{{ $message->question }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 回答部分 -->
                            <div class="mb-6">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-lg">🤖</span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                                            <div class="flex justify-between items-center mb-2">
                                                <div class="font-bold text-green-800 dark:text-green-200 text-sm">
                                                    Laraiの回答
                                                </div>
                                                <button onclick='copyToClipboard(@json($message->response))'
                                                        class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 text-xs"
                                                        title="回答をコピー">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                            <div class="text-green-700 dark:text-green-300 whitespace-pre-wrap leading-relaxed">{{ $message->response }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($loop->last)
                                <!-- 最後のメッセージだけ線を入れる -->
                                <div class="border-t-2 border-gray-200 dark:border-gray-700 my-6"></div>
                            @endif
                        @endforeach
                    </div>

                    <!-- 続けて質問するフォーム -->
                    <div class="mb-6">
                        <h4 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">
                            <i class="fas fa-comment-dots mr-2"></i>続けて質問する
                        </h4>

                        <form method="POST" action="{{ route('larai.store') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $root->id }}">

                            <div>
                                <textarea
                                    id="question"
                                    name="question"
                                    rows="4"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 resize-none"
                                    placeholder="次の質問を入力してください..."
                                    required>{{ old('question') }}</textarea>

                                @error('question')
                                    <p class="text-red-500 text-sm mt-2">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror

                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    最大1000文字まで入力できます
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex gap-3 items-center">
                                    <a href="{{ route('larai.index') }}"
                                       class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 font-medium transition duration-300">
                                        <i class="fas fa-list mr-2"></i>会話一覧
                                    </a>
                                    <a href="{{ route('larai.create') }}"
                                       class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 font-medium transition duration-300">
                                        <i class="fas fa-plus mr-2"></i>新しい会話
                                    </a>

                                    <!-- 削除ボタン -->
                                    <button type="button"
                                            onclick="if(confirm('この会話スレッドを削除してもよろしいですか？（すべての質問と回答が削除されます）')) { document.getElementById('delete-form').submit(); }"
                                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-medium transition duration-300"
                                            title="会話スレッドを削除">
                                        <i class="fas fa-trash mr-2"></i>削除
                                    </button>
                                </div>

                                <button type="submit"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md transition duration-300 transform hover:scale-105">
                                    <i class="fas fa-paper-plane mr-2"></i>質問する
                                </button>
                            </div>
                        </form>

                        <!-- 削除用の非表示フォーム -->
                        <form id="delete-form" action="{{ route('larai.destroy', $root) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // 簡単な成功通知
                const button = event.target.closest('button');
                const originalHTML = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i> コピーしました！';

                setTimeout(() => {
                    button.innerHTML = originalHTML;
                }, 2000);
            }).catch(function(err) {
                console.error('コピーに失敗しました:', err);
            });
        }
    </script>
</x-app-layout>