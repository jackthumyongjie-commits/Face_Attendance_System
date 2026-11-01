<?php

namespace App\Services;

class UploadService
{
    public function saveBase64Image(string $base64, string $subdir): ?string
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
            $ext = strtolower($matches[1]);
            if ($ext === 'jpeg') $ext = 'jpg';
            $base64 = substr($base64, strpos($base64, ',') + 1);
        } else {
            $ext = 'jpg';
        }

        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return null;
        }

        $data = base64_decode($base64, true);
        if ($data === false) {
            return null;
        }

        $dir = public_path('uploads/' . trim($subdir, '/'));
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $full = $dir . '/' . $filename;
        if (file_put_contents($full, $data) === false) {
            return null;
        }

        return 'uploads/' . trim($subdir, '/') . '/' . $filename;
    }

    public function saveLeaveDocument(array $file): array
    {
        $maxSize = config('app.upload_max_size', 5242880);
        $allowedExt = ['pdf', 'jpg', 'jpeg', 'png'];
        $allowedMime = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/jpg',
        ];

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'File upload failed.'];
        }
        if (($file['size'] ?? 0) > $maxSize) {
            return ['success' => false, 'message' => 'File exceeds maximum size limit.'];
        }

        $original = $file['name'] ?? 'document';
        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            return ['success' => false, 'message' => 'Invalid file type. Allowed: PDF, JPG, JPEG, PNG.'];
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $allowedMime, true)) {
            return ['success' => false, 'message' => 'Invalid file MIME type.'];
        }

        $dir = public_path('uploads/leave');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest = $dir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['success' => false, 'message' => 'Unable to save uploaded file.'];
        }

        return [
            'success' => true,
            'path' => 'uploads/leave/' . $filename,
            'original_name' => $original,
        ];
    }
}
