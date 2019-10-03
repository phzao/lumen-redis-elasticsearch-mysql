<?php declare(strict_types=1);

namespace App\Utils\Hash;

/**
 * Interface HashInterface
 */
interface HashInterface
{
    /**
     * @param string $text
     *
     * @return string
     */
    public function getCrypto(string $text):string;
}
