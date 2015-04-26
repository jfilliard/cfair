<?php

namespace spec\CFair\UseCase\Database;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

class DatabaseConsumerUseCaseSpec extends ObjectBehavior
{
    public function let(
        Connection $connection,
        LoggerInterface $logger
    ) {
        $this->beConstructedWith($connection, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CFair\UseCase\Database\DatabaseConsumerUseCase');
        $this->shouldImplement('CFair\UseCase\ConsumerUseCase');
    }

    public function it_should_save_a_job_in_database(
        Connection $connection,
        LoggerInterface $logger
    ) {
        $message = ['some' => 'data'];
        $jobId = 42;
        $connection->insert('job', ['payload' => json_encode($message)])->shouldBeCalled();
        $connection->lastInsertId()->willReturn($jobId);
        $this->consume($message)->shouldReturn($jobId);
    }
}
