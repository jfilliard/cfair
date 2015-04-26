<?php

namespace CFair\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use CFair\Tool\ResetDatabaseTool;

class ResetController
{
    private $resetDatabaseTool;
    private $urlGenerator;

    public function __construct(ResetDatabaseTool $resetDatabaseTool, UrlGeneratorInterface $urlGenerator)
    {
        $this->resetDatabaseTool = $resetDatabaseTool;
        $this->urlGenerator      = $urlGenerator;
    }

    public function exec()
    {
        $this->resetDatabaseTool->exec();
        return new RedirectResponse($this->urlGenerator->generate('overview'), 303);
    }
}
