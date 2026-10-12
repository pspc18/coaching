<?php

namespace {
    if (!function_exists('mb_strwidth')) {
        function mb_strwidth($string, $encoding = 'UTF-8') {
            return function_exists('mb_strlen') ? mb_strlen((string) $string, $encoding) : strlen((string) $string);
        }
    }

    if (!function_exists('mb_strimwidth')) {
        function mb_strimwidth($string, $start, $width, $trimmarker = '', $encoding = null) {
            $string = (string) $string;
            $trimmarker = (string) $trimmarker;
            $start = (int) $start;
            $width = (int) $width;

            if (function_exists('mb_substr') && function_exists('mb_strwidth')) {
                $encoding = $encoding ?: (function_exists('mb_internal_encoding') ? mb_internal_encoding() : 'UTF-8');
                if (mb_strwidth($string, $encoding) <= $width) {
                    return $string;
                }
                $markerWidth = mb_strwidth($trimmarker, $encoding);
                $targetWidth = max(0, $width - $markerWidth);
                $trimmed = mb_substr($string, $start, $targetWidth, $encoding);
                while (mb_strwidth($trimmed, $encoding) > $targetWidth && mb_strlen($trimmed, $encoding) > 0) {
                    $trimmed = mb_substr($trimmed, 0, -1, $encoding);
                }
                return $trimmed . $trimmarker;
            }

            $len = function_exists('mb_strlen') ? mb_strlen($string, 'UTF-8') : strlen($string);
            if ($len <= $width) {
                return $string;
            }
            $markerLen = function_exists('mb_strlen') ? mb_strlen($trimmarker, 'UTF-8') : strlen($trimmarker);
            $targetWidth = max(0, $width - $markerLen);
            $sub = function_exists('mb_substr') ? mb_substr($string, $start, $targetWidth, 'UTF-8') : substr($string, $start, $targetWidth);
            return $sub . $trimmarker;
        }
    }
}

namespace Illuminate\Support {
    if (!function_exists('Illuminate\Support\mb_strwidth')) {
        function mb_strwidth($string, $encoding = 'UTF-8') {
            return \mb_strwidth($string, $encoding);
        }
    }

    if (!function_exists('Illuminate\Support\mb_strimwidth')) {
        function mb_strimwidth($string, $start, $width, $trimmarker = '', $encoding = null) {
            return \mb_strimwidth($string, $start, $width, $trimmarker, $encoding);
        }
    }
}
