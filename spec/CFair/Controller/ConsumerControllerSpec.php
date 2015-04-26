<?php

namespace spec\CFair\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use CFair\UseCase\ConsumerUseCase;

class ConsumerControllerSpec extends ObjectBehavior
{
    public function let(
        ConsumerUseCase $consumerUseCase,
        LoggerInterface $logger
    ) {
        $this->beConstructedWith($consumerUseCase, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CFair\Controller\ConsumerController');
    }

    function it_should_handle_a_json_message(
        ConsumerUseCase $consumerUseCase,
        Request $request
    ) {
        $request->getContent()->willReturn('{');
        $consumerUseCase->consume()->shouldNotBeCalled();
        $response = $this->exec($request);
        $response->shouldHaveType('Symfony\Component\HttpFoundation\Response');
        $response->shouldBeClientError();
    }

    function it_should_reject_bad_json(
        ConsumerUseCase $consumerUseCase,
        Request $request
    ) {
        $message = ['some' => 'data'];
        $jobId = 42;
        $request->getContent()->willReturn(json_encode($message));
        $consumerUseCase->consume($message)->willReturn($jobId);
        $consumerUseCase->consume($message)->shouldBeCalled();
        $response = $this->exec($request);
        $response->shouldHaveType('Symfony\Component\HttpFoundation\JsonResponse');
        $response->shouldBeSuccessful();
        $response->getStatusCode()->shouldReturn(202);
        $response->getContent()->shouldReturn(json_encode(['job_id' => $jobId]));
    }
}
