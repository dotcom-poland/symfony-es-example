<?php

declare(strict_types=1);

namespace Test\App\SharedKernel\Es\Implementation\BrokenEntityExample;

use App\SharedKernel\Es\Contract\EventInterface;

final class BrokenFactoryEvent implements EventInterface
{
    public static function fromArray(array $eventData): self
    {
        throw new \Exception();
    }

    public function toArray(): array
    {
        return ['foo' => 'bar'];
    }
}
