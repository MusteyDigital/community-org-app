<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioSmsService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.auth_token')
        );
    }

    public function send(string $to, string $message): void
    {
        if (! $to) {
            return;
        }

        try {
            $this->client->messages->create($to, [
                'from' => config('services.twilio.from_number'),
                'body' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('Twilio SMS failed: '.$e->getMessage());
        }
    }
}