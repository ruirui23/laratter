<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';
    }

    public function generateResponse(string $question): array
    {
        try {
            $url = $this->baseUrl . '/gemini-2.5-flash:generateContent?key=' . $this->apiKey;

            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $question
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 2048,
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return [
                        'success' => true,
                        'response' => $data['candidates'][0]['content']['parts'][0]['text'],
                        'metadata' => [
                            'model' => 'gemini-2.5-flash',
                            'timestamp' => now()->toISOString(),
                            'usage' => $data['usageMetadata'] ?? null
                        ]
                    ];
                } else {
                    Log::error('Gemini API response format error', ['response' => $data]);
                    return [
                        'success' => false,
                        'response' => 'すみません、回答を生成できませんでした。',
                        'error' => 'Invalid response format'
                    ];
                }
            } else {
                Log::error('Gemini API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return [
                    'success' => false,
                    'response' => 'エラーが発生しました。後でもう一度お試しください。',
                    'error' => 'API request failed: ' . $response->status()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Gemini API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'response' => 'システムエラーが発生しました。しばらく経ってからお試しください。',
                'error' => $e->getMessage()
            ];
        }
    }
}