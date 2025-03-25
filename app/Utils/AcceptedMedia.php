<?php

namespace App\Utils;

class AcceptedMedia
{
    public static function getAcceptedMediaTypes(): array
    {
        return [
            // Documents
            'application/pdf',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            // Images - excluding SVG for security
            'image/png',
            'image/jpeg',
            'image/jpg',
            // Common safe formats
            'image/gif',
            'image/bmp',
            'image/tiff',
            'application/zip',
            'application/x-rar-compressed',
            'text/plain',
            'text/csv',
        ];
    }
}
