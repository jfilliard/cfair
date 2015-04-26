<?php

namespace CFair\Tool;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use DateTime;
use Exception;

class ProcessorTool {
    private $connection;
    private $logger;

    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger     = $logger;
    }

    public function process($limit, $delay)
    {
        $jobProcessed = 0;
        while ($jobProcessed < $limit && $job = $this->findOne($delay)) {
            $this->processOne($job);
            $jobProcessed++;
        }
        $this->logger->notice($jobProcessed.' job(s) have processed');
    }

    private function findOne($delay)
    {
        $query = 'SELECT id, payload FROM job WHERE processed_at IS NULL ORDER BY created_at ASC LIMIT 1';
        do {
            $job = $this->connection->query($query)->fetch();
        } while (!$job && $delay && sleep($delay) === 0);
        return $job;
    }

    private function processOne($job)
    {
        $id = (int)$job['id'];
        $payload = json_decode($job['payload'], true);
        $this->connection->transactional(function($connection) use ($id, $payload) {
            $this->processJob($connection, $id, $payload);
        });
        return $id;
    }

    private function processJob(Connection $connection, $id, Array $payload)
    {
        $this->logger->debug('starting processing job '.$id);
        try {
            $connection->insert('`order`', [
                'user_id'             => $payload['userId'],
                'placed_at'           => new DateTime($payload['timePlaced']),
                'amount_buy'          => $payload['amountBuy'],
                'amount_sell'         => $payload['amountSell'],
                'currency_from'       => $payload['currencyFrom'],
                'currency_to'         => $payload['currencyTo'],
                'rate'                => $payload['rate'],
                'originating_country' => $payload['originatingCountry'],
            ], ['placed_at' => 'datetime']);
            $query = <<<SQL
    INSERT INTO `currency_stats` (`currency`, `amount_from`, `amount_to`)
    VALUES (:currency, :amountFrom, 0)
    ON DUPLICATE KEY UPDATE `amount_from` = `amount_from` + :amountFrom
SQL;
            $connection->prepare($query)->execute([
                'currency'   => $payload['currencyFrom'],
                'amountFrom' => $payload['amountSell'],
            ]);
            $query = <<<SQL
    INSERT INTO `currency_stats` (`currency`, `amount_from`, `amount_to`)
    VALUES (:currency, 0, :amountTo)
    ON DUPLICATE KEY UPDATE `amount_to` = `amount_to` + :amountTo
SQL;
            $connection->prepare($query)->execute([
                'currency' => $payload['currencyTo'],
                'amountTo' => $payload['amountBuy'],
            ]);
        }
        catch (Exception $e) {
            $this->logger->debug('job '.$id.' have failed');
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            // the job should be me marked as failed but it's not supported here
            // to avoid blocking on a failling job, we still mark it as processed instead
        }
        $connection->update('job', ['processed_at' => new DateTime], ['id' => $id], ['datetime']);
        $this->logger->debug('job '.$id.' has been processed');
    }
}
