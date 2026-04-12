<?php

namespace Siarko\Events\Api;

interface EventListenerInterface
{
    public function consumeEvent($event);
}