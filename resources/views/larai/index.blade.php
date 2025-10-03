<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Larai - AI Chat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 成功メッセージ -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium">過去の会話</h3>
                            <a href="{{ route('larai.create') }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                                <i class="fas fa-plus mr-2"></i>新しい質問をする
                            </a>
                        </div>
                    </div>

                    @if($conversations->count() > 0)
                        <div class="space-y-4">
                            @foreach($conversations as $conversation)
                                <div class="border dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-300 cursor-pointer"
                                     onclick="window.location='{{ route('larai.show', $conversation) }}'">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="font-bold text-blue-600 dark:text-blue-400 text-sm">
                                                会話スレッド
                                            </div>
                                            @if($conversation->children->count() > 0)
                                                <span class="text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded">
                                                    {{ $conversation->children->count() + 1 }}件の質問
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $conversation->created_at->format('Y/m/d H:i') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 text-gray-800 dark:text-gray-200">
                                        <span class="font-semibold">最初の質問:</span> {{ Str::limit($conversation->question, 150) }}
                                    </div>

                                    <div class="mb-2">
                                        <div class="font-bold text-green-600 dark:text-green-400 text-sm">
                                            Laraiの回答:
                                        </div>
                                    </div>
                                    <div class="text-gray-600 dark:text-gray-300 mb-3">
                                        {{ Str::limit($conversation->response, 200) }}
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <div class="flex gap-3 items-center">
                                            <a href="{{ route('larai.show', $conversation) }}"
                                               onclick="event.stopPropagation()"
                                               class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium transition duration-300">
                                                会話を見る →
                                            </a>
                                            <form action="{{ route('larai.destroy', $conversation) }}" method="POST"
                                                  onsubmit="return confirm('この会話スレッドを削除してもよろしいですか？（すべての質問と回答が削除されます）');"
                                                  onclick="event.stopPropagation()"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-sm transition duration-300"
                                                        title="削除">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                        @if($conversation->metadata)
                                            <div class="text-xs text-gray-400">
                                                Model: {{ $conversation->metadata['model'] ?? 'gemini-2.5-flash' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $conversations->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-500 dark:text-gray-400 mb-4">
                                <i class="fas fa-comments text-6xl mb-4"></i>
                                <p class="text-lg">まだ会話がありません</p>
                                <p class="text-sm">Laraiに何でも質問してみましょう！</p>
                            </div>
                            <a href="{{ route('larai.create') }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-md transition duration-300">
                                最初の質問をする
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>