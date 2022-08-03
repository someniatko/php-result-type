<?php

declare(strict_types=1);

namespace Someniatko\ResultType;

/**
 * @template-covariant TError
 * @template-extends Result<never, TError>
 * @psalm-immutable
 */
final class Error extends Result
{
    /** @var TError */
    private $value;

    /** @param TError $value */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function map(callable $map): ResultInterface
    {
        // no success value, so nothing to map.
        return $this;
    }

    public function mapError(callable $map): ResultInterface
    {
        return new self($map($this->value));
    }

    public function chain(callable $map): ResultInterface
    {
        // no success value, so nothing to map.
        return $this;
    }

    /** @return TError */
    public function get()
    {
        return $this->value;
    }

    public function getOr(callable $map)
    {
        return $map($this->value);
    }

    public function getOrThrow(\Throwable $e)
    {
        throw $e;
    }

    public function ensure(callable $condition, $else): ResultInterface
    {
        return $this;
    }
}
