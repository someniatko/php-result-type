<?php

declare(strict_types=1);

namespace Test;

use PHPUnit\Framework\TestCase;
use Someniatko\ResultType\Error;
use Someniatko\ResultType\Success;

final class ResultTest extends TestCase
{
    public function testReadmeExample(): void
    {
        $value = (new Success('Let it be'))
            ->map(fn (string $s) => substr_count($s, ' '))
            ->chain(
                fn (int $wordsCount) => $wordsCount > 3
                    ? new Success('Long text')
                    : new Error('short text')
            )
            ->map(fn (string $s) => str_replace(' ', '', $s))
            ->mapError(fn (string $s) => strtoupper($s))
            ->get();

        self::assertSame('SHORT TEXT', $value);
    }

    public function testMapWithSuccess(): void
    {
        $initial = new Success(1500);
        $mapped = $initial->map(fn (int $val) => $val / 2);
        self::assertSame(750, $mapped->get());
    }

    public function testMapWithError(): void
    {
        $initial = new Error(1500);
        $mapped = $initial->map(fn (int $val) => $val / 2);
        self::assertSame(1500, $mapped->get());
    }

    public function testMapErrorWithSuccess(): void
    {
        $initial = new Success(1500);
        $mapped = $initial->mapError(fn (int $val) => $val / 2);
        self::assertSame(1500, $mapped->get());
    }

    public function testMapErrorWithError(): void
    {
        $initial = new Error(1500);
        $mapped = $initial->mapError(fn (int $val) => $val / 2);
        self::assertSame(750, $mapped->get());
    }

    public function testMapAfterChainReturningSuccessUsingSuccess(): void
    {
        $initial = new Success('Hello');
        $chained = $initial->chain(fn (string $successResult) => new Success($successResult . ', world!'));
        $mapped = $chained->map('strtoupper');
        self::assertSame('HELLO, WORLD!', $mapped->get());
    }

    public function testMapAfterChainReturningErrorUsingSuccess(): void
    {
        $initial = new Success('Hello');
        $chained = $initial->chain(fn (string $successResult) => new Error($successResult . ', error occurred!'));
        $mapped = $chained->map('strtoupper');
        self::assertEquals('Hello, error occurred!', $mapped->get());
    }

    public function testMapAfterChainReturningSuccessUsingError(): void
    {
        $initial = new Error('Hello');
        $chained = $initial->chain(fn (string $successResult) => new Success($successResult . ', world!'));
        $mapped = $chained->map('strtoupper');
        self::assertSame('Hello', $mapped->get());
    }
}
