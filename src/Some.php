<?php

declare(strict_types=1);

namespace Someniatko\ResultType;

/**
 * @template-covariant T
 * @template-extends Option<T>
 */
final class Some extends Option
{
    /**
     * @readonly
     * @var T
     */
    private $value;

    /**
     * @psalm-mutation-free
     * @param T $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /** @psalm-mutation-free */
    public function map(callable $map): Option
    {
        return new self($map($this->value));
    }

    /** @psalm-mutation-free */
    public function flatMap(callable $map): Option
    {
        return $map($this->value);
    }

    /** @psalm-mutation-free */
    public function getOr(callable $else)
    {
        return $this->value;
    }

    /** @psalm-mutation-free */
    public function getOrElse($else)
    {
        return $this->value;
    }

    /** @psalm-mutation-free */
    public function getOrThrow(\Throwable $e)
    {
        return $this->value;
    }

    public function process(callable $ifSome, callable $ifNone): void
    {
        $ifSome($this->value);
    }
}
