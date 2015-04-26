<?php

use Assert\Assertion;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Monolog\Handler\NullHandler;
use CFair\Application;

/**
 * Behat context class.
 */
class CommonContext implements SnippetAcceptingContext
{
    use ApplicationContext;

    /**
     * @BeforeScenario
     */
    public function cleanDB(BeforeScenarioScope $scope)
    {
        $this->application()['reset-database.tool']->exec();
    }

    /**
     * @Then a job should be queued
     */
    public function aJobShouldBeQueued()
    {
        $query = 'SELECT COUNT(*) FROM job WHERE processed_at IS NULL';
        if ($this->application()['db']->query($query)->fetchColumn() < 1 ) {
            throw new Exception('no job in queue');
        }
    }

    /**
     * @Given there is a pending job
     */
    public function thereIsAPendingJob(PyStringNode $payload)
    {
        $this->application()['db']->insert('job', [
            'payload' => $payload,
            'created_at' => new DateTime,
        ], ['created_at' => 'datetime']);
    }

    /**
     * @Then no job should still be pending
     */
    public function noJobShouldStillBePending()
    {
        $query = 'SELECT COUNT(*) FROM job WHERE processed_at IS NULL';
        if ($this->application()['db']->query($query)->fetchColumn() != 0 ) {
            throw new Exception('job queue is not empty');
        }
    }

    /**
     * @Then user :userId should have one order placed at :placedAt
     */
    public function userShouldHaveOneOrderPlacedAt($userId, $placedAt)
    {
        $query = 'SELECT COUNT(*) FROM `order` WHERE user_id = :userId AND placed_at = :placedAt';
        $params = [
            'userId' => $userId,
            'placedAt' => new DateTime($placedAt),
        ];
        $types = ['placedAt' => 'datetime'];
        if ($this->application()['db']->executeQuery($query, $params, $types)->fetchColumn() == 0 ) {
            throw new Exception('order does not exists');
        }
    }

    /**
     * @Then stats for currency :currency should be :from - :to
     */
    public function statsForCurrencyShouldBe($currency, $from, $to)
    {
        $query = <<<SQL
    SELECT COUNT(*)
    FROM `currency_stats`
    WHERE currency = :currency AND amount_from = :amountFrom AND amount_to = :amountTo
SQL;
        $params = [
            'currency'   => $currency,
            'amountFrom' => $from,
            'amountTo'   => $to,
        ];
        if ($this->application()['db']->executeQuery($query, $params)->fetchColumn() == 0 ) {
            throw new Exception('stats does not matches');
        }
    }

}
