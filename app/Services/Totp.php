<?php

namespace App\Services;

/**
 * RFC 6238 TOTP implementation.
 * Compatible with Google Authenticator, Microsoft Authenticator, Authy, 1Password, etc.
 *
 * Pure-PHP, no external dependencies.
 */
class Totp
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    private const PERIOD   = 30;
    private const DIGITS   = 6;

    /** Generate a random base32-encoded secret (default 16 chars = 80 bits). */
    public static function generateSecret(int $length = 16): string
    {
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::ALPHABET[random_int(0, 31)];
        }
        return $secret;
    }

    /** Generate the current 6-digit TOTP code. */
    public static function code(string $secret, ?int $time = null): string
    {
        $time    = $time ?? time();
        $counter = (int) floor($time / self::PERIOD);
        $key     = self::base32Decode($secret);

        // Pack counter as 8-byte big-endian
        $binCounter = pack('N*', 0) . pack('N*', $counter);
        $hash       = hash_hmac('sha1', $binCounter, $key, true);

        // Dynamic truncation
        $offset = ord($hash[19]) & 0xf;
        $code   = (
            ((ord($hash[$offset + 0]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
             (ord($hash[$offset + 3]) & 0xff)
        ) % (10 ** self::DIGITS);

        return str_pad((string) $code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    /** Verify a 6-digit code against the secret, with a small time window for clock drift. */
    public static function verify(string $secret, string $code, int $window = 1): bool
    {
        $code = trim(str_replace(' ', '', $code));
        if (!preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        $time = time();
        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals(self::code($secret, $time + ($i * self::PERIOD)), $code)) {
                return true;
            }
        }
        return false;
    }

    /** Build the otpauth:// URI for QR code generation. */
    public static function provisioningUri(string $secret, string $accountName, string $issuer = 'CHMSU GMS'): string
    {
        $params = http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
        ]);
        $label = rawurlencode($issuer . ':' . $accountName);
        return "otpauth://totp/{$label}?{$params}";
    }

    private static function base32Decode(string $secret): string
    {
        $secret = strtoupper(rtrim($secret, '='));
        $bits   = '';
        foreach (str_split($secret) as $char) {
            $idx = strpos(self::ALPHABET, $char);
            if ($idx === false) continue;
            $bits .= str_pad(decbin($idx), 5, '0', STR_PAD_LEFT);
        }
        $output = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $output .= chr(bindec($byte));
            }
        }
        return $output;
    }
}
