<?php

declare(strict_types=1);

namespace Test;

use PHPUnit\Framework\TestCase;
use Someniatko\ResultType\Error;
use Someniatko\ResultType\ResultInterface;
use Someniatko\ResultType\Success;

final class ResultTest extends TestCase
{
    public function testReadmeExample(): void
    {
        /** @var ResultInterface<string, string> $initial */
        $initial = new Success('Let it be');
        $value = $initial
            ->map(fn (string $s): int => substr_count($s, ' '))
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
        /** @var ResultInterface<int, int> $initial */
        $initial = new Success(1500);
        $mapped = $initial->map(fn (int $val) => $val / 2);
        self::assertEquals(750, $mapped->get());
    }

    public function testMapWithError(): void
    {
        /** @var ResultInterface<int, int> $initial */
        $initial = new Error(1500);
        $mapped = $initial->map(fn (int $val) => $val / 2);
        self::assertEquals(1500, $mapped->get());
    }

    public function testMapErrorWithSuccess(): void
    {
        /** @var ResultInterface<int, int> $initial */
        $initial = new Success(1500);
        $mapped = $initial->mapError(fn (int $val) => $val / 2);
        self::assertEquals(1500, $mapped->get());
    }

    public function testMapErrorWithError(): void
    {
        /** @var ResultInterface<int, int> $initial */
        $initial = new Error(1500);
        $mapped = $initial->mapError(fn (int $val) => $val / 2);
        self::assertEquals(750, $mapped->get());
    }

    public function testMapAfterChainReturningSuccessUsingSuccess(): void
    {
        /** @var ResultInterface<string, string> $initial */
        $initial = new Success('Hello');
        $chained = $initial->chain(fn (string $successResult) => new Success($successResult . ', world!'));
        $mapped = $chained->map('strtoupper');
        self::assertEquals('HELLO, WORLD!', $mapped->get());
    }

    public function testMapAfterChainReturningErrorUsingSuccess(): void
    {
        /** @var ResultInterface<string, string> $initial */
        $initial = new Success('Hello');
        $chained = $initial->chain(fn (string $successResult) => new Error($successResult . ', error occurred!'));
        $mapped = $chained->map('strtoupper');
        self::assertEquals('Hello, error occurred!', $mapped->get());
    }

    public function testMapAfterChainReturningSuccessUsingError(): void
    {
        /** @var ResultInterface<string, string> $initial */
        $initial = new Error('Hello');
        $chained = $initial->chain(fn (string $successResult) => new Success($successResult . ', world!'));
        $mapped = $chained->map('strtoupper');
        self::assertEquals('Hello', $mapped->get());
    }
}
