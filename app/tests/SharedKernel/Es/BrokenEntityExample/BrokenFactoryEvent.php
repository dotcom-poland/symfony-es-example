<?php

declare(strict_types=1);

namespace Test\App\SharedKernel\Es\BrokenEntityExample;

use App\Es\Contract\EventInterface;

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
