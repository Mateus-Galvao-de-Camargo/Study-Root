<?php
/**
 * Wrapper sobre password_hash / password_verify do PHP.
 *
 * A versão antiga deste arquivo implementava bcrypt manualmente com
 * crypt() + salt gerado por mt_rand()/uniqid(). Tanto a geração quanto o
 * código eram frágeis. Esta versão delega para as funções nativas, que
 * usam um CSPRNG do sistema e fazem comparação em tempo constante.
 *
 * A interface pública (Bcrypt::hash, Bcrypt::check) é mantida para evitar
 * mudanças em chamadas existentes. Hashes legados com prefixo $2a$ gerados
 * pela versão antiga continuam compatíveis porque password_verify aceita
 * qualquer hash bcrypt válido.
 */

declare(strict_types=1);

class Bcrypt
{
    public static function hash(string $password, ?int $cost = null): string
    {
        $options = [];
        if ($cost !== null) {
            $options['cost'] = max(4, min(12, $cost));
        }
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    public static function check(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function needsRehash(string $hash, ?int $cost = null): bool
    {
        $options = [];
        if ($cost !== null) {
            $options['cost'] = max(4, min(12, $cost));
        }
        return password_needs_rehash($hash, PASSWORD_BCRYPT, $options);
    }
}
