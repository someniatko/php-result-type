<?php

declare(strict_types=1);

namespace Someniatko\ResultType;

/**
 * @template-extends Option<never>
 */
final class None extends Option
{
    /** @psalm-mutation-free */
    public function map(callable $map): Option
    {
        return $this;
    }

    /** @psalm-mutation-free */
    public function flatMap(callable $map): Option
    {
        return $this;
    }

    /** @psalm-mutation-free */
    public function getOr(callable $else)
    {
        return $else();
    }

    /** @psalm-mutation-free */
    public function getOrElse($else)
    {
        return $else;
    }

    /** @psalm-mutation-free */
    public function getOrThrow(\Throwable $e)
    {
        throw $e;
    }

    public function process(callable $ifSome, callable $ifNone): void
    {
        $ifNone();
    }
}
