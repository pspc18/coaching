<?php

namespace App\Jobs;

use App\Models\MessageQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Http\Client\Response;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $messages; // अब multiple messages handle करेंगे

    public function __construct($messages)
    {
        // एक या multiple messages आ सकते हैं
        $this->messages = is_array($messages) ? $messages : [$messages];
    }

public function handle()
{
    if (empty($this->messages)) {
        Log::error("❌ No messages to send in job.");
        return;
    }

    $responses = Http::pool(function ($pool) {
        $requests = [];

        foreach ($this->messages as $message) {
            $url = 'https://whatsapp.rusofterp.in/api/send?' . http_build_query([
                'username' => 'rusoft',
                'message'  => $message->content,
                'token'    => '1dc0f86d3dfb9c192037b3c1d82cdd99',
                'type'     => 'send',
                'number'   => '91' . $message->receiver_number,
            ]);

            $requests[] = $pool->timeout(5)->get($url);
        }

        return $requests;
    });

    foreach ($responses as $index => $response) {
        $message = $this->messages[$index];

        if ($response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
            // ✅ Success Case
            $message->message_status = 1;
            $message->sent_at = now();
            $message->response = $response->body();
            $message->save();

            Log::info("✅ Sent to {$message->receiver_number}");
        } else {
            // ✅ Failure Case
            $errorMessage = $response instanceof \Illuminate\Http\Client\Response
                ? $response->body()
                : ($response instanceof \Throwable ? $response->getMessage() : 'Unknown error');

            $message->message_status = 2;
            $message->response = $errorMessage;
            $message->save();

            Log::error("❌ Failed to send to {$message->receiver_number} — {$errorMessage}");
        }
    }
}



}
