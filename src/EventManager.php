<?php

namespace Siarko\Events;

class EventManager
{

    /**
     * @param EventListenerInterface[][] $listeners
     */
    public function __construct(
        private array $listeners = []
    )
    {
    }

    /**
     * @param string $event
     * @param EventListenerInterface $handler
     * @return void
     */
    public function register(string $event, EventListenerInterface $handler): void
    {
        if(!array_key_exists($event, $this->listeners)){
            $this->listeners[$event] = [];
        }
        $this->listeners[$event][] = $handler;
    }

    /**
     * @param string $event
     * @param mixed|null $data
     * @return void
     */
    public function dispatch(string $event, mixed $data = null): void
    {
        if(array_key_exists($event, $this->listeners)){
            foreach($this->listeners[$event] as $handler){
                $handler->execute($data);
            }
        }
    }

}