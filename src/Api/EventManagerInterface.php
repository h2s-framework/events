<?php

namespace Siarko\Events\Api;

interface EventManagerInterface
{

    public function addListener(callable|EventListenerInterface $listener): void;

    public function queueEvent(EventInterface $event, bool $postpone = false): void;

    public function consumeEvents(): void;
}