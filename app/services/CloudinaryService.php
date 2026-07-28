<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class CloudinaryService
{
    public function uploadMany(array $files, string $folder, array &$storedLocalPaths = []): array
    {
        $media = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $media[] = $this->uploadEvidence($file, $folder, $storedLocalPaths);
            }
        }

        return $media;
    }

    public function uploadEvidence(UploadedFile $file, string $folder, array &$storedLocalPaths = []): array
    {
        $mimeType = (string) $file->getMimeType();
        $mediaType = str_starts_with($mimeType, 'video/') ? 'video' : 'image';

        if (app()->environment('testing')) {
            return $this->fakeCloudinaryUpload(
                $folder,
                $file->getClientOriginalName(),
                $mimeType,
                $mediaType,
                (int) $file->getSize()
            );
        }

        if (! $this->isConfigured()) {
            throw new \RuntimeException('Chưa cấu hình Cloudinary. Vui lòng kiểm tra CLOUDINARY_API_SECRET trong file .env.');
        }

        try {
            return $this->uploadCloudinary($file, $folder, $mimeType, $mediaType);
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Không thể upload minh chứng lên Cloudinary: '.$exception->getMessage());
        }
    }

    public function uploadLocalFile(
        string $filePath,
        string $folder,
        ?string $originalName = null,
        ?string $mimeType = null,
        ?string $mediaType = null
    ): array {
        if (! $this->isConfigured()) {
            throw new \RuntimeException('Chưa cấu hình Cloudinary. Vui lòng kiểm tra CLOUDINARY_API_SECRET trong file .env.');
        }

        if (! is_file($filePath)) {
            throw new \RuntimeException('Không tìm thấy file minh chứng: '.$filePath);
        }

        $mimeType = $mimeType ?: (mime_content_type($filePath) ?: 'application/octet-stream');
        $mediaType = $mediaType ?: (str_starts_with($mimeType, 'video/') ? 'video' : 'image');

        return $this->uploadPathToCloudinary(
            $filePath,
            $folder,
            $originalName ?: basename($filePath),
            $mimeType,
            $mediaType,
            (int) filesize($filePath)
        );
    }

    public function mediaUrl(array|object $media): string
    {
        $disk = data_get($media, 'disk', 'public');
        $path = data_get($media, 'path', '');

        if ($disk === 'cloudinary' || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/'.$path);
    }

    private function isConfigured(): bool
    {
        return filter_var(config('cloudinary.enabled'), FILTER_VALIDATE_BOOL)
            && filled(config('cloudinary.cloud_name'))
            && filled(config('cloudinary.api_key'))
            && filled(config('cloudinary.api_secret'));
    }

    private function uploadCloudinary(UploadedFile $file, string $folder, string $mimeType, string $mediaType): array
    {
        return $this->uploadPathToCloudinary(
            $file->getRealPath(),
            $folder,
            $file->getClientOriginalName(),
            $mimeType,
            $mediaType,
            (int) $file->getSize()
        );
    }

    private function uploadPathToCloudinary(
        string $filePath,
        string $folder,
        string $fileName,
        string $mimeType,
        string $mediaType,
        int $size
    ): array
    {
        $timestamp = time();
        $signature = $this->makeSignature([
            'folder' => $folder,
            'timestamp' => $timestamp,
        ]);

        $url = sprintf(
            'https://api.cloudinary.com/v1_1/%s/auto/upload',
            config('cloudinary.cloud_name')
        );

        $handle = fopen($filePath, 'r');

        try {
            $request = Http::timeout(90);

            if (! filter_var(config('cloudinary.verify_ssl'), FILTER_VALIDATE_BOOL)) {
                $request = $request->withoutVerifying();
            }

            $response = $request
                ->attach('file', $handle, $fileName)
                ->post($url, [
                    'api_key' => config('cloudinary.api_key'),
                    'folder' => $folder,
                    'timestamp' => $timestamp,
                    'signature' => $signature,
                ])
                ->throw()
                ->json();
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }
        return [
            'disk' => 'cloudinary',
            'path' => $response['secure_url'] ?? '',
            'public_id' => $response['public_id'] ?? null,
            'original_name' => $fileName,
            'mime_type' => $mimeType,
            'media_type' => $mediaType,
            'size' => $size,
        ];
    }

    private function makeSignature(array $params): string
    {
        ksort($params);

        $payload = collect($params)
            ->map(fn ($value, $key) => $key.'='.$value)
            ->implode('&');

        return sha1($payload.config('cloudinary.api_secret'));
    }

    private function fakeCloudinaryUpload(
        string $folder,
        string $fileName,
        string $mimeType,
        string $mediaType,
        int $size
    ): array {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $name = pathinfo($fileName, PATHINFO_FILENAME) ?: 'evidence';
        $publicId = trim($folder, '/').'/test-'.uniqid().'-'.$name;
        $path = collect(explode('/', $publicId))
            ->map(fn ($part) => rawurlencode($part))
            ->implode('/');

        if ($extension) {
            $path .= '.'.$extension;
        }

        return [
            'disk' => 'cloudinary',
            'path' => 'https://res.cloudinary.com/testing/'.$mediaType.'/upload/'.$path,
            'public_id' => $publicId,
            'original_name' => $fileName,
            'mime_type' => $mimeType,
            'media_type' => $mediaType,
            'size' => $size,
        ];
    }

}
