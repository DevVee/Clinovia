<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Setting;
use App\Models\SmsLog;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class SmsService
{
    private string $apiKey;
    private string $senderName;
    private string $apiUrl = 'https://api.semaphore.co/api/v4/messages';

    public function __construct()
    {
        $this->apiKey     = config('semaphore.api_key', '');
        $this->senderName = config('semaphore.sender_name', 'ICCBI');
    }

    /**
     * Send an SMS and log the result.
     */
    public function send(
        string  $number,
        string  $message,
        ?string $recipientName = null,
        mixed   $reference     = null
    ): SmsLog {
        $log = SmsLog::create([
            'recipient_number' => $number,
            'recipient_name'   => $recipientName,
            'message'          => $message,
            'status'           => 'pending',
            'reference_id'     => $reference?->id,
            'reference_type'   => $reference ? get_class($reference) : null,
            'created_by'       => auth()->id(),
        ]);

        if (empty($this->apiKey)) {
            $log->update([
                'status'        => 'failed',
                'error_message' => 'Semaphore API key not configured.',
            ]);
            return $log;
        }

        try {
            $client   = new Client(['timeout' => 15]);
            $response = $client->post($this->apiUrl, [
                'form_params' => [
                    'apikey'     => $this->apiKey,
                    'number'     => $number,
                    'message'    => $message,
                    'sendername' => $this->senderName,
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            $log->update([
                'status'       => 'sent',
                'sent_at'      => now(),
                'api_response' => $body,
            ]);

        } catch (GuzzleException $e) {
            $log->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }

        return $log;
    }

    /**
     * Notify patient their appointment was approved.
     *
     * Template variables: {name}, {date}, {time}
     * Customisable via Settings: sms_template_approval
     */
    public function sendAppointmentApproval(Appointment $appointment): ?SmsLog
    {
        if (! $appointment->patient->contact_number) {
            return null;
        }

        $date = $appointment->appointment_date->format('F d, Y');
        $time = \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A');
        $name = $appointment->patient->first_name;

        $template = Setting::get(
            'sms_template_approval',
            'Dear {name}, your appointment at ICCBI Clinic on {date} at {time} has been approved. Please arrive 10 minutes early. - SSCMS'
        );

        $message = str_replace(
            ['{name}', '{date}', '{time}'],
            [$name,    $date,    $time],
            $template
        );

        return $this->send(
            $appointment->patient->contact_number,
            $message,
            $appointment->patient->full_name,
            $appointment
        );
    }

    /**
     * Notify patient their appointment was cancelled.
     *
     * Template variables: {name}, {date}, {reason}
     * Customisable via Settings: sms_template_cancellation
     */
    public function sendAppointmentCancellation(Appointment $appointment, string $reason): ?SmsLog
    {
        if (! $appointment->patient->contact_number) {
            return null;
        }

        $date = $appointment->appointment_date->format('F d, Y');
        $name = $appointment->patient->first_name;

        $template = Setting::get(
            'sms_template_cancellation',
            'Dear {name}, your appointment on {date} at ICCBI Clinic has been cancelled. Reason: {reason}. Please contact us to reschedule. - SSCMS'
        );

        $message = str_replace(
            ['{name}', '{date}', '{reason}'],
            [$name,    $date,    $reason],
            $template
        );

        return $this->send(
            $appointment->patient->contact_number,
            $message,
            $appointment->patient->full_name,
            $appointment
        );
    }
}
