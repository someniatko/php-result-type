<?php

declare(strict_types=1);

namespace Test;

use PHPUnit\Framework\TestCase;
use Someniatko\ResultType\Option;

final class OptionTest extends TestCase
{
    public function testMapSome(): void
    {
        $initial = Option::some(1500);
        $mapped = $initial->map(fn(int $val) => $val / 2);
        self::assertEquals(750, $mapped->getOrElse(null));
    }

    public function testMapNone(): void
    {
        /** @var Option<int> $initial */
        $initial = Option::none();
        $mapped = $initial->map(fn(int $val) => $val / 2);
        self::assertEquals(null, $mapped->getOrElse(null));
    }

    public function testFlatMapSomeToSome(): void
    {
        $initial = Option::some(1500);
        $mapped = $initial->flatMap(fn (int $val) => Option::some($val / 2));
        self::assertEquals(750, $mapped->getOrElse(null));
    }

    public function testFlatMapSomeToNone(): void
    {
        $initial = Option::some(1500);
        $mapped = $initial->flatMap(fn () => Option::none());
        self::assertEquals(null, $mapped->getOrElse(null));
    }

    public function testFlatMapNoneToSome(): void
    {
        /** @var Option<int> $initial */
        $initial = Option::none();
        $mapped = $initial->flatMap(fn (int $val) => Option::some($val / 2));
        self::assertEquals(null, $mapped->getOrElse(null));
    }

    public function testFlatMapNoneToNone(): void
    {
        $initial = Option::none();
        $mapped = $initial->flatMap(fn () => Option::none());
        self::assertEquals(null, $mapped->getOrElse(null));
    }

    public function testGetOrSome(): void
    {
        $option = Option::some(1500);
        $unwrapped = $option->getOr(fn() => -1);
        self::assertEquals(1500, $unwrapped);
    }

    public function testGetOrNone(): void
    {
        $option = Option::none();
        $unwrapped = $option->getOr(fn() => -1);
        self::assertEquals(-1, $unwrapped);
    }

    public function testGetOrNoneClosureThrows(): void
    {
        $option = Option::none();

        $this->expectException(\RuntimeException::class);

        /** @psalm-suppress UnusedMethodCall */
        $option->getOr(function () {
            throw new \RuntimeException('expected');
        });
    }

    public function testGetOrThrowSome(): void
    {
        $option = Option::some(1500);
        $unwrapped = $option->getOrThrow(new \RuntimeException('unexpected'));
        self::assertEquals(1500, $unwrapped);
    }

    public function testGetOrThrowNone(): void
    {
        $option = Option::none();

        $this->expectException(\RuntimeException::class);
        /** @psalm-suppress UnusedMethodCall */
        $option->getOrThrow(new \RuntimeException('expected'));
    }
}
