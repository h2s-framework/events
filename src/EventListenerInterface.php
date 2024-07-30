<?php

declare(strict_types=1);

namespace Siarko\Events;

interface EventListenerInterface
{

    /**
     * Implement this method to handle event
     * @param mixed|null $data
     * @return void
     */
    public function execute(mixed $data = null): void;
}