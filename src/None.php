<?php

declare(strict_types=1);

namespace Someniatko\ResultType;

/**
 * @psalm-immutable
 * @template-extends Option<never>
 */
final class None extends Option
{
    public function map(callable $map): Option
    {
        return $this;
    }

    public function flatMap(callable $map): Option
    {
        return $this;
    }

    public function getOr(callable $else)
    {
        return $else();
    }

    public function getOrElse($else)
    {
        return $else;
    }

    public function getOrThrow(\Throwable $e)
    {
        throw $e;
    }

    public function ensure(callable $condition): Option
    {
        return $this;
    }
}
