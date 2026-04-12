<?php

namespace Siarko\Events;

use Psr\Log\LoggerInterface;
use Siarko\Events\Api\EventInterface;
use Siarko\Events\Api\EventListenerInterface;
use Throwable;

class EventManager
{

    /**
     * @var EventInterface[]
     */
    private array $eventQueue = [];

    /**
     * @param array<callable|EventListenerInterface> $consumers
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private array $consumers = [],
        private bool  $consumeImmediately = true,
        private bool $throwOnFirstConsumerError = false,
    )
    {
    }

    public function setConsumeImmediately(bool $consumeImmediately): void
    {
        $this->consumeImmediately = $consumeImmediately;
    }

    public function setThrowOnFirstConsumerError(bool $throwOnFirstConsumerError): void
    {
        $this->throwOnFirstConsumerError = $throwOnFirstConsumerError;
    }

    /**
     * @throws \Exception
     */
    public function addListener(callable|EventListenerInterface $listener): void
    {
        $type = $this->readArgumentType($listener);
        if (!$type) {
            throw new \Exception("Listener argument must have a type");
        }
        if (!($this->consumers[$type] ?? false)) {
            $this->consumers[$type] = [];
        }
        $this->consumers[$type][] = $listener;
    }

    public function queueEvent(EventInterface $event, bool $postpone = false): void
    {
        $this->eventQueue[] = $event;
        if ($this->consumeImmediately && !$postpone) {
            $this->consumeEvents();
        }
    }

    public function consumeEvents(): void
    {
        while ($event = array_shift($this->eventQueue)) {
            $this->consume($event);
        }
    }

    /**
     * @throws Throwable
     */
    private function consume(EventInterface $event): void
    {
        foreach ($this->consumers as $eventType => $consumers) {
            if ($event instanceof $eventType) {
                try{
                    foreach ($consumers as $consumer) {
                        if ($consumer instanceof EventListenerInterface) {
                            $consumer->consumeEvent($event);
                        } else {
                            $consumer($event);
                        }
                    }
                }catch (Throwable $e){
                    $this->logger->critical($e);
                    if ($this->throwOnFirstConsumerError) {
                        throw $e;
                    }
                }
            }
        }
    }

    /**
     * @throws \ReflectionException
     */
    private function readArgumentType(callable|EventListenerInterface $listener): ?string
    {
        if ($listener instanceof EventListenerInterface) {
            $reflection = new \ReflectionClass($listener);
            $methodReflection = $reflection->getMethod('consumeEvent');
        } else {
            $methodReflection = new \ReflectionFunction($listener);
        }
        if ($methodReflection->getNumberOfParameters() !== 1) {
            return null;
        }
        $paramType = $methodReflection->getParameters()[0]->getType();
        return $paramType?->getName();
    }

}