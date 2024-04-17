<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Database\PDO;

use Exception;
use PDO;

final readonly class PDODatabaseFactory
{
    protected const array DRIVER_OPTIONS = [
        'mysql' => [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        ],
        'sqlite' => [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    ];

    public static function create(array $configuration): PDODatabase
    {
        if ($configuration['driver'] === 'sqlite') {
            $conf = $configuration['host'];
        } elseif ($configuration['driver'] === 'mysql') {
            $conf = sprintf(
                'host=%s;dbname=%s;charset=%s;port=%s',
                $configuration['host'],
                $configuration['database'] ?? '',
                $configuration['charset'] ?? '',
                $configuration['port'] ?? ''
            );
        } else {
            throw new Exception('Unsupported driver');
        }

        $dsn = sprintf('%s:%s', $configuration['driver'], $conf);

        $pdo = new PDO($dsn, $configuration['user'] ?? null, $configuration['password'] ?? null, self::DRIVER_OPTIONS[$configuration['driver']]);

        return new PDODatabase($pdo);
    }
}
