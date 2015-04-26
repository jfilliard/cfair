<?php

namespace CFair\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;
use Psr\Log\LoggerInterface;
use DateTime;
use NumberFormatter;
use PDO;

class OverviewController {
    private $connection;
    private $twig;

    public function __construct(Connection $connection, Twig_Environment $twig)
    {
        $this->connection = $connection;
        $this->twig       = $twig;
    }

    public function index()
    {
        $query = <<<SQL
    SELECT
        SUM(processed_at IS NULL) AS to_be_processed,
        MIN(IF(processed_at IS NULL, created_at, NULL)) oldest_to_be_processed,
        SUM(processed_at IS NOT NULL) AS processed,
        SUM(processed_at IS NOT NULL AND created_at > :oneHourAgo) AS processed_last_hour
    FROM `job`
SQL;
        $data = $this->connection->executeQuery($query, [
            'oneHourAgo' => new DateTime('1 hour ago')
        ], ['oneHourAgo' => 'datetime'])->fetch();

        $data['oldest_to_be_processed'] = new DateTime($data['oldest_to_be_processed']);

        $data['late']         = $data['oldest_to_be_processed'] < new DateTime('10 minutes ago');
        $data['verylate']     = $data['oldest_to_be_processed'] < new DateTime('30 hour ago');
        $data['veryverylate'] = $data['oldest_to_be_processed'] < new DateTime('1 hour ago');


        $query = <<<SQL
    SELECT
        currency, amount_from, amount_to
    FROM `currency_stats`
    ORDER BY currency ASC
SQL;
        $data['stats'] = $this->connection->executeQuery($query)->fetchAll();

        $formatter = new NumberFormatter('en_IE', NumberFormatter::CURRENCY);
        foreach ($data['stats'] as &$row) {
            $row['amount_from'] = $formatter->formatCurrency($row['amount_from'], $row['currency']);
            $row['amount_to']   = $formatter->formatCurrency($row['amount_to'], $row['currency']);
        }

        return $this->twig->render('overview/index.twig.html', $data);
    }
}
