<?php

declare(strict_types=1);

final class Database
{
    public static function connect(array $config): PDO
    {
        $url = (string) (getenv('DATABASE_URL') ?: getenv('POSTGRES_URL') ?: '');

        if ($url !== '') {
            $parts = parse_url($url);

            if (!$parts || empty($parts['host'])) {
                throw new RuntimeException('DATABASE_URL no es valida.');
            }

            $query = [];
            parse_str($parts['query'] ?? '', $query);

            $dsn = 'pgsql:host=' . $parts['host']
                . ';port=' . ($parts['port'] ?? 5432)
                . ';dbname=' . ltrim($parts['path'] ?? '', '/')
                . ';sslmode=' . ($query['sslmode'] ?? 'require');

            return new PDO(
                $dsn,
                urldecode($parts['user'] ?? ''),
                urldecode($parts['pass'] ?? ''),
                self::options()
            );
        }

        return new PDO(
            'pgsql:host=' . (getenv('PGHOST') ?: 'localhost')
                . ';port=' . (getenv('PGPORT') ?: '5432')
                . ';dbname=' . (getenv('PGDATABASE') ?: $config['name']),
            getenv('PGUSER') ?: $config['user'],
            getenv('PGPASSWORD') ?: $config['pass'],
            self::options()
        );
    }

    private static function options(): array
    {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
    }
}
