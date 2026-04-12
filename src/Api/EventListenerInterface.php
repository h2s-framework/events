<?php

namespace Siarko\Events\Api;

interface EventListenerInterface
{
    public function consumeEvent(EventInterface $event);
}