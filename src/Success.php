<?php

declare(strict_types=1);

namespace Someniatko\ResultType;

/**
 * @template-covariant TSuccess
 * @template-extends Result<TSuccess, never>
 * @psalm-immutable
 */
final class Success extends Result
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

    /** @return TSuccess */
    public function get()
    {
        return $this->value;
    }

    public function getOr(callable $map)
    {
        return $this->value;
    }

    public function getOrThrow(\Throwable $e)
    {
        return $this->value;
    }

    public function ensure(callable $condition, $else): ResultInterface
    {
        return $condition($this->value)
            ? $this
            : Result::error($else);
    }
}
