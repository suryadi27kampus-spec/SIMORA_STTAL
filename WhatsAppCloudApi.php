<?php
declare(strict_types=1);

namespace Simora;

use RuntimeException;

/** Sends an approved one-variable template through Meta WhatsApp Cloud API. */
final class WhatsAppCloudApi
{
    public function __construct(
        private readonly string $apiVersion,
        private readonly string $phoneNumberId,
        private readonly string $accessToken,
        private readonly string $templateName,
        private readonly string $templateLanguage = 'id',
        private readonly int $timeoutSeconds = 20,
    ) {
        if (!preg_match('/^v\d+\.\d+$/', $apiVersion)) throw new RuntimeException('Versi API WhatsApp belum dikonfigurasi.');
        if (!preg_match('/^\d+$/', $phoneNumberId)) throw new RuntimeException('Phone Number ID belum dikonfigurasi.');
        if ($accessToken === '') throw new RuntimeException('Token akses WhatsApp belum tersedia pada environment.');
        if (!preg_match('/^[a-z0-9_]{2,512}$/', $templateName)) throw new RuntimeException('Nama template WhatsApp belum dikonfigurasi.');
        if (!preg_match('/^[a-z]{2}(?:_[A-Z]{2})?$/', $templateLanguage)) throw new RuntimeException('Kode bahasa template tidak valid.');
        if ($timeoutSeconds < 5 || $timeoutSeconds > 120) throw new RuntimeException('Batas waktu permintaan harus 5–120 detik.');
    }

    /** @return array{message_id:?string,response:array<string,mixed>} */
    public function sendTextTemplate(string $destination, string $message): array
    {
        if (!function_exists('curl_init')) throw new RuntimeException('Ekstensi cURL PHP belum aktif.');
        $to = $this->normalizeIndonesianNumber($destination);
        $url = sprintf('https://graph.facebook.com/%s/%s/messages', $this->apiVersion, $this->phoneNumberId);
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $this->templateName,
                'language' => ['code' => $this->templateLanguage],
                'components' => [[
                    'type' => 'body',
                    'parameters' => [['type' => 'text', 'text' => $message]],
                ]],
            ],
        ];
        $curl = curl_init($url);
        if ($curl === false) throw new RuntimeException('Koneksi ke layanan WhatsApp tidak dapat disiapkan.');
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => min(10, $this->timeoutSeconds),
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
        ]);
        $responseBody = curl_exec($curl);
        $curlError = curl_error($curl);
        $statusCode = (int)curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        if ($responseBody === false) throw new RuntimeException('Permintaan WhatsApp gagal: ' . $curlError);
        $response = json_decode((string)$responseBody, true);
        if (!is_array($response)) throw new RuntimeException('Layanan WhatsApp mengembalikan respons yang tidak dapat dibaca.');
        if ($statusCode < 200 || $statusCode >= 300) {
            $reason = $response['error']['message'] ?? ('HTTP ' . $statusCode);
            throw new RuntimeException('WhatsApp menolak pesan: ' . (string)$reason);
        }
        $messageId = $response['messages'][0]['id'] ?? null;
        return ['message_id' => is_string($messageId) ? $messageId : null, 'response' => $response];
    }

    private function normalizeIndonesianNumber(string $number): string
    {
        $digits = preg_replace('/\D+/', '', $number) ?? '';
        if (str_starts_with($digits, '0')) $digits = '62' . substr($digits, 1);
        elseif (str_starts_with($digits, '8')) $digits = '62' . $digits;
        if (!preg_match('/^62\d{8,13}$/', $digits)) throw new RuntimeException('Nomor WhatsApp harus nomor Indonesia yang valid dengan kode negara +62.');
        return $digits;
    }
}
