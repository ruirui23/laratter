<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laraiに質問する') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <div class="text-center">
                            <div class="text-4xl mb-4">🤖</div>
                            <h3 class="text-2xl font-bold mb-2">Laraiに質問してみましょう</h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                何でも気軽に質問してください。プログラミング、一般知識、アドバイスなど、幅広い質問にお答えします。
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('larai.store') }}" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="question" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                質問内容
                            </label>
                            <textarea 
                                id="question" 
                                name="question" 
                                rows="6"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 resize-none"
                                placeholder="例: Laravelでバリデーションを実装する方法を教えて
例: 効率的な学習方法について教えて
例: おすすめのプログラミング言語は？"
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

                        <div class="bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-400 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        <strong>ヒント:</strong> 具体的で明確な質問をすると、より良い回答が得られます。
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <a href="{{ route('larai.index') }}" 
                               class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 font-medium transition duration-300">
                                <i class="fas fa-arrow-left mr-2"></i>会話一覧に戻る
                            </a>
                            
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-md transition duration-300 transform hover:scale-105">
                                <i class="fas fa-paper-plane mr-2"></i>質問する
                            </button>
                        </div>
                    </form>

                    <div class="mt-8 border-t dark:border-gray-700 pt-6">
                        <h4 class="text-lg font-medium mb-3">質問例</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-300" 
                                 onclick="document.getElementById('question').value = 'Laravelでデータベースマイグレーションを使用する方法を教えてください'">
                                <div class="text-sm font-medium">📚 Laravel学習</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">データベースマイグレーションについて</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-300"
                                 onclick="document.getElementById('question').value = 'プログラミング初心者におすすめの学習方法は？'">
                                <div class="text-sm font-medium">🎯 学習相談</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">効率的な学習方法について</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>