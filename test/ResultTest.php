<?php

declare(strict_types=1);

namespace Test;

use PHPUnit\Framework\TestCase;
use Someniatko\ResultType\Error;
use Someniatko\ResultType\Result;
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

    public function testGetSuccessOrOnSuccess(): void
    {
        /** @var ResultInterface<int, int> $result */
        $result = new Success(1500);

        $unwrapped = $result->getOr(fn(int $error) => $error + 1);
        self::assertEquals(1500, $unwrapped);
    }

    public function testGetSuccessOrOnError(): void
    {
        /** @var ResultInterface<int, int> $result */
        $result = new Error(1500);

        $unwrapped = $result->getOr(fn(int $error) => $error + 1);
        self::assertEquals(1501, $unwrapped);
    }

    public function testGetSuccessOrOnErrorClosureWithoutArgs(): void
    {
        /** @var ResultInterface<int, int> $result */
        $result = new Error(1500);

        $unwrapped = $result->getOr(fn() => 1);
        self::assertEquals(1, $unwrapped);
    }

    public function testGetSuccessOrOnErrorClosureThrows(): void
    {
        /** @var ResultInterface<int, int> $result */
        $result = new Error(1500);

        $this->expectException(\RuntimeException::class);

        /** @psalm-suppress UnusedMethodCall */
        $result->getOr(function () {
            throw new \RuntimeException('expected');
        });
    }

    public function testGetOrThrowOnSuccess(): void
    {
        /** @var ResultInterface<int, int> $result */
        $result = new Success(1500);

        $unwrapped = $result->getOrThrow(new \RuntimeException('unexpected'));
        self::assertEquals(1500, $unwrapped);
    }

    public function testGetOrThrowOnError(): void
    {
        /** @var ResultInterface<int, int> $result */
        $result = new Error(1500);

        $this->expectException(\RuntimeException::class);
        /** @psalm-suppress UnusedMethodCall */
        $result->getOrThrow(new \RuntimeException('expected'));
    }

    public function testSuccesses(): void
    {
        $results = [
            Result::success(1),
            Result::success(2),
            Result::error(3),
            Result::success(4),
            Result::error(5),
        ];

        self::assertEquals([ 1, 2, 4 ], Result::extractSuccesses($results));
    }

    public function testErrors(): void
    {
        $results = [
            Result::success(1),
            Result::success(2),
            Result::error(3),
            Result::success(4),
            Result::error(5),
        ];

        self::assertEquals([ 3, 5 ], Result::extractErrors($results));
    }

    public function testEnsureSuccessReturningTrue(): void
    {
        $result = Result::success(123);
        $ensured = $result->ensure(fn(int $i) => $i > 100, 'failed');
        self::assertEquals(123, $ensured->get());
    }

    public function testEnsureSuccessReturningFalse(): void
    {
        $result = Result::success(123);
        $ensured = $result->ensure(fn(int $i) => $i < 100, 'failed');
        self::assertEquals('failed', $ensured->get());
    }

    public function testEnsureErrorDoesNotChangePreviousError(): void
    {
        $result = Result::error('old error');
        $ensured = $result->ensure(fn(int $i) => $i > 100, 'new error');
        self::assertEquals('old error', $ensured->get());
    }
}
