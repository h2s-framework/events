### Event manager

IMPORTANT - Consumer methods / callables require a type. This type is used to figure out on
what event should consumer be executed.
Missing type will throw Exception.
If you want to execute for any Event - use `EventInterface`.
EventManager uses `instanceof` to check if event can be used for a consumer

```php
$logger = new BasicLogger(__DIR__.'/logs.log');
// Initilize new instance
// Logger is required to log uncaught exceptions from each consumer. 
// One consumer's exception doesn't have to stop others
$eventManager = new EventManager($logger);

// Optional, set by default
// This will cause execution of consumers at the moment of queueEvent()
$eventManager->setConsumeImmediately(true);

// If you want...
$eventManager->setThrowOnFirstConsumerError(true);
// It will use logger first and then throw

// Anonymous classes can be registered - as well as just objects implementing the ConsumerInterface
$eventManager->addListener(new class implements ConsumerInterface {
    public function consumeEvent(EventInterface $event)
    {
        echo "[ANY EVENT] ";
    }
});

// Callables can be registered
$eventManager->addListener(function(EventInitialized $event) use($bootstrap) {
    echo "[INITIALIZED, running at {$bootstrap->getTargetTPS()} TPS]\n";
});

// And you post / execute consumers
$eventManager->queueEvent(new \Siarko\CliBootstrap\Events\EventInitialized());

// Also, if setConsumeImmediately(false), consumers won't be executed.
// Events will be queued and then consumers will execute at once with
$eventManager->consumeEvents();

// If you want to postpone execution of a selected event, use
$eventManager->queueEvent(new \Siarko\CliBootstrap\Events\EventInitialized(), postpone: true);
//It will execute the consumers for this event either with next normal event execution of with
$eventManager->consumeEvents();
```