<?php

namespace CFair\Tool;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use DateTime;

class ProcessorTool {
	private $connection;
	private $logger;

	public function __construct(Connection $connection, LoggerInterface $logger)
	{
		$this->connection = $connection;
		$this->logger     = $logger;
	}

	public function processAll()
	{
		$query = 'SELECT id, payload FROM job WHERE processed_at IS NULL ORDER BY created_at ASC';
		foreach ($this->connection->query($query) as $row) {
			$this->connection->transactional(function($connection) use ($row) {
				$id = $row['id'];
				$payload = json_decode($row['payload'], true);
				$this->logger->debug('starting processing job '.$id);
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
				$connection->update('job', ['processed_at' => new DateTime], ['id' => $id], ['datetime']);
				$this->logger->debug('job '.$id.' has been processed');
			});
		}
	}
}
