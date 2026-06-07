<?php

namespace App\Services;

use App\Models\AiConversation;
use App\Models\Setting;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class AiAssistantService
{
    private string $apiKey;
    private string $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = env('GROQ_API_KEY', '');
    }

    private function model(): string
    {
        return Setting::get('ai_model', 'llama-3.3-70b-versatile');
    }

    private function systemPrompt(): string
    {
        $clinic = Setting::get('clinic_name', 'ICCBI School Clinic');
        $org    = Setting::get('org_name', 'Immaculate Conception College of Balayan, Inc.');
        $system = Setting::get('app_short_name', 'SSCMS');

        return <<<PROMPT
You are Cobi, the intelligent AI assistant for {$system} (Smart School Clinic Management System) at {$org}. You assist clinic staff at {$clinic}.

Your capabilities:
- Help users navigate and use SSCMS features (Patients, Appointments, Consultations, Medicines, Inventory, Dispensing, Reports, SMS Notifications, Audit Logs, Settings)
- Answer questions about clinic administration, workflows, and best practices
- Explain medical terminology in simple language appropriate for clinic staff
- Help interpret clinic data and reports
- Guide users step-by-step through system tasks
- Answer general health, nursing, and medical knowledge questions

System modules overview:
- Patient Records: full CRUD with categories (college, senior_high, junior_high, elementary, kinder, daycare, teacher, employee, visitor)
- Appointments: scheduling with time slots, approve/cancel/no-show workflow, SMS notifications
- Consultations: medical visits linked to patients and optional appointments
- Medicines: catalog with categories, stock tracking, expiry alerts
- Inventory: stock-in, stock-out, dispensed transactions ledger
- Dispensing: dispense medicines to patients linked to consultations
- Reports: daily, monthly, annual, medicine usage, inventory snapshot — PDF/CSV export
- SMS: Semaphore API integration, customizable appointment templates
- Audit Logs: full activity tracking with before/after values

Guidelines:
- Be concise but thorough — give actionable answers
- If asked about specific patient records, explain you don't have direct database access but guide the user to find it in the system
- Never provide specific medical diagnoses or prescribe medications
- Always maintain professional clinic staff tone
- When explaining how to do something in SSCMS, give step-by-step instructions
PROMPT;
    }

    /**
     * Send a message and get a response. Passes the last 5 conversations as context.
     */
    public function chat(string $message, Collection $history): string
    {
        if (empty($this->apiKey)) {
            return "⚠️ Groq API key is not configured. Please ask your administrator to add `GROQ_API_KEY` to the `.env` file.";
        }

        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt()],
        ];

        // Include past conversation for context (last 5, oldest first)
        foreach ($history->reverse() as $convo) {
            $messages[] = ['role' => 'user',      'content' => $convo->message];
            $messages[] = ['role' => 'assistant', 'content' => $convo->response ?? ''];
        }

        $messages[] = ['role' => 'user', 'content' => $message];

        try {
            $client   = new Client(['timeout' => 45]);
            $response = $client->post($this->apiUrl, [
                'headers' => [
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model'       => $this->model(),
                    'messages'    => $messages,
                    'max_tokens'  => 1024,
                    'temperature' => 0.7,
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            return $body['choices'][0]['message']['content']
                ?? 'I could not generate a response. Please try again.';

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $body = json_decode($e->getResponse()->getBody()->getContents(), true);
            return "API error: " . ($body['error']['message'] ?? $e->getMessage());
        } catch (\Throwable $e) {
            return "Sorry, I encountered an error connecting to the AI service: " . $e->getMessage();
        }
    }
}
