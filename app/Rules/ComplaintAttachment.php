<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;

class ComplaintAttachment implements Rule
{
    public function passes($attribute, $value)
    {
        if (!$value instanceof UploadedFile || !$value->isValid()) {
            return false;
        }

        $extension = strtolower($value->getClientOriginalExtension());
        $path = $value->getRealPath();

        if (!$path || !in_array($extension, ['pdf', 'jpg', 'jpeg', 'png'], true)) {
            return false;
        }

        if ($extension === 'pdf') {
            $handle = @fopen($path, 'rb');
            if (!$handle) {
                return false;
            }

            $header = fread($handle, 1024);
            fclose($handle);

            return is_string($header) && strpos($header, '%PDF-') !== false;
        }

        $image = @getimagesize($path);
        if ($image === false) {
            return false;
        }

        $expectedType = $extension === 'png' ? IMAGETYPE_PNG : IMAGETYPE_JPEG;

        return ($image[2] ?? null) === $expectedType;
    }

    public function message()
    {
        return 'The attachment must be a valid PDF, JPG, JPEG or PNG file.';
    }
}
