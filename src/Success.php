<?php

declare(strict_types=1);

namespace Someniatko\ResultType;

/**
 * @template-covariant TSuccess
 * @template-implements ResultInterface<TSuccess, never-return>
 * @psalm-immutable
 */
final class Success implements ResultInterface
{
    /** @var TSuccess */
    private $value;

    /** @param TSuccess $value */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function map(callable $map): ResultInterface
    {
        return new self($map($this->value));
    }

    public function mapError(callable $map): ResultInterface
    {
        // no error, so nothing to map.
        return $this;
    }

    public function chain(callable $map): ResultInterface
    {
        return $map($this->value);
    }

    public function get()
    {
        return $this->value;
    }
}
