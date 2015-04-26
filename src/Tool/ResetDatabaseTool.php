<?php

namespace CFair\Tool;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

class ResetDatabaseTool {
    private $connection;
    private $logger;

    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger     = $logger;
    }

    public function exec()
    {
        $queries = [];
        $queries[] = <<<SQL
    DROP TABLE IF EXISTS `job`, `order`, `currency_stats`;
SQL;
        $queries[] = <<<SQL
    CREATE TABLE `job` (
        `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `created_at` datetime NOT NULL,
        `processed_at` datetime NULL,
        `payload` text COLLATE 'utf8_general_ci' NOT NULL
    ) COMMENT='quick & dirty MQ' ENGINE='InnoDB' COLLATE 'utf8_general_ci';
SQL;
        $queries[] = <<<SQL
    CREATE TABLE `order` (
        `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `user_id` int unsigned NOT NULL,
        `placed_at` datetime NOT NULL,
        `amount_buy` DECIMAL(10,2) NOT NULL,
        `amount_sell` DECIMAL(10,2) NOT NULL,
        `currency_from` CHAR(3) NOT NULL,
        `currency_to` CHAR(3) NOT NULL,
        `rate` DECIMAL(5,4) NOT NULL,
        `originating_country` CHAR(2) NOT NULL
    ) ENGINE='InnoDB' COLLATE 'utf8_general_ci';
SQL;
        $queries[] = <<<SQL
    CREATE TABLE `currency_stats` (
        `currency` CHAR(3) NOT NULL PRIMARY KEY,
        `amount_from` DECIMAL(10,2) NOT NULL,
        `amount_to` DECIMAL(10,2) NOT NULL
    ) ENGINE='InnoDB' COLLATE 'utf8_general_ci';
SQL;
        foreach ($queries as $query) {
            $this->logger->debug($query);
            $this->connection->executeQuery($query);
        }
        $this->logger->info('Database has been reset');
    }
}
