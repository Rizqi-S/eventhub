<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CloudinaryService
{
    private function credentials(): array
    {
        $cloud = (string) config('services.cloudinary.cloud_name');
        $key = (string) config('services.cloudinary.api_key');
        $secret = (string) config('services.cloudinary.api_secret');

        if ($cloud === '' || $key === '' || $secret === '') {
            throw new RuntimeException('Konfigurasi Cloudinary belum lengkap. Isi CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, dan CLOUDINARY_API_SECRET.');
        }

        return [$cloud, $key, $secret];
    }

    public function upload(UploadedFile $file): array
    {
        [$cloud, $key, $secret] = $this->credentials();
        $timestamp = time();
        $folder = 'eventhub/posters';
        $signature = sha1("folder={$folder}&timestamp={$timestamp}{$secret}");

        $response = Http::timeout(45)
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post("https://api.cloudinary.com/v1_1/{$cloud}/image/upload", [
                'api_key' => $key,
                'timestamp' => $timestamp,
                'folder' => $folder,
                'signature' => $signature,
            ]);

        if (! $response->successful() || ! $response->json('secure_url') || ! $response->json('public_id')) {
            throw new RuntimeException('Upload poster ke Cloudinary gagal: '.($response->json('error.message') ?? 'respons tidak valid.'));
        }

        return [
            'url' => $response->json('secure_url'),
            'public_id' => $response->json('public_id'),
        ];
    }

    public function delete(?string $publicId): void
    {
        if (! $publicId) return;

        [$cloud, $key, $secret] = $this->credentials();
        $timestamp = time();
        $signature = sha1("public_id={$publicId}&timestamp={$timestamp}{$secret}");

        $response = Http::asForm()->timeout(30)->post(
            "https://api.cloudinary.com/v1_1/{$cloud}/image/destroy",
            [
                'public_id' => $publicId,
                'api_key' => $key,
                'timestamp' => $timestamp,
                'signature' => $signature,
            ]
        );

        if (! $response->successful() || ! in_array($response->json('result'), ['ok', 'not found'], true)) {
            throw new RuntimeException('Penghapusan poster lama dari Cloudinary gagal.');
        }
    }
}
