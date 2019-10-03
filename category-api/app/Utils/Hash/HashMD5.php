<?php declare(strict_types=1);

namespace App\Utils\Hash;

/**
 * Class HashMD5
 */
class HashMD5 implements HashInterface
{
    /**
     * @param string $text
     *
     * @return string
     */
    public function getCrypto(string $text): string
    {
        return md5($text);
    }
}
