<?php

declare(strict_types=1);

namespace Someniatko\ResultType;

/**
 * @psalm-immutable
 * @template-covariant T
 * @template-extends Option<T>
 */
final class Some extends Option
{
    /** @var T */
    private $value;

    /**
     * @param T $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function map(callable $map): Option
    {
        return new self($map($this->value));
    }

    public function flatMap(callable $map): Option
    {
        return $map($this->value);
    }

    public function getOr(callable $else)
    {
        return $this->value;
    }

    public function getOrElse($else)
    {
        return $this->value;
    }

    public function getOrThrow(\Throwable $e)
    {
        return $this->value;
    }
}
