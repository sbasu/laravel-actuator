<?php

declare(strict_types=1);

if (!function_exists('format_bytes')) {
    function format_bytes(int|float $bytes): string
    {
        if ($bytes < 0) {
            return '0 B';
        }

        if ($bytes === 0 || $bytes === 0.0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $power = (int) floor(log((float) $bytes, 1024));
        $power = min($power, count($units) - 1);

        $value = $bytes / (1024 ** $power);

        return round($value, 2) . ' ' . $units[$power];
    }
}

if (!function_exists('format_uptime')) {
    function format_uptime(int $seconds): string
    {
        if ($seconds <= 0) {
            return '0 seconds';
        }

        $days    = (int) floor($seconds / 86400);
        $hours   = (int) floor(($seconds % 86400) / 3600);
        $minutes = (int) floor(($seconds % 3600) / 60);
        $secs    = $seconds % 60;

        $parts = [];

        if ($days > 0) {
            $parts[] = $days . ' ' . ($days === 1 ? 'day' : 'days');
        }

        if ($hours > 0) {
            $parts[] = $hours . ' ' . ($hours === 1 ? 'hour' : 'hours');
        }

        if ($minutes > 0) {
            $parts[] = $minutes . ' ' . ($minutes === 1 ? 'minute' : 'minutes');
        }

        if ($secs > 0 && empty($parts)) {
            $parts[] = $secs . ' ' . ($secs === 1 ? 'second' : 'seconds');
        }

        return implode(' ', $parts);
    }
}

if (!function_exists('human_timestamp')) {
    function human_timestamp(): string
    {
        return gmdate('Y-m-d\TH:i:s\Z');
    }
}
