<?php

namespace CFair\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use CFair\UseCase\ConsumerUseCase;

class ConsumerController {
    private $consumerUseCase;
    private $logger;

    public function __construct(ConsumerUseCase $consumerUseCase, LoggerInterface $logger)
    {
        $this->consumerUseCase = $consumerUseCase;
        $this->logger          = $logger;
    }

    public function exec(Request $request)
    {
        $content = $request->getContent();
        $json = json_decode($content, true);
        if ($json === null) {
            $this->logger->warning('Json could not be decoded', ['content' => $content]);
            return new Response('', 400);
        }
        return new JsonResponse(['job_id' => $this->consumerUseCase->consume($json)], 202);
    }
}
