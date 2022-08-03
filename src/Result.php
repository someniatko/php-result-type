<?php

declare(strict_types=1);

namespace Someniatko\ResultType;

/**
 * @template-covariant TSuccess
 * @template-covariant TError
 * @template-implements ResultInterface<TSuccess, TError>
 * @psalm-immutable
 */
abstract class Result implements ResultInterface
{
    /**
     * @psalm-pure
     * @template T
     * @param T $value
     * @return Success<T>
     */
    public static function success($value): Success
    {
        return new Success($value);
    }

    /**
     * @psalm-pure
     * @template T
     * @param T $value
     * @return Error<T>
     */
    public static function error($value): Error
    {
        return new Error($value);
    }

    /**
     * Filters and extracts values only of Success results from the given array.
     *
     * @template S
     * @param list<ResultInterface<S, mixed>> $results
     * @return list<S>
     */
    public static function extractSuccesses(array $results): array
    {
        return array_reduce(
            $results,
            /**
             * @param list<S> $carry
             * @param ResultInterface<S, mixed> $result
             * @return list<S>
             */
            static fn(array $carry, ResultInterface $result) => $result
                ->mapError(fn() => $carry)
                ->map(
                    /**
                     * @param S $t
                     * @return list<S>
                     */
                    fn($t) => array_merge($carry, [ $t ])
                )
                ->get(),
            [],
        );
    }

    /**
     * Filters and extracts values only of Error results from the given array.
     *
     * @template E
     * @param list<ResultInterface<mixed, E>> $results
     * @return list<E>
     */
    public static function extractErrors(array $results): array
    {
        return array_reduce(
            $results,
            /**
             * @param list<E> $carry
             * @param ResultInterface<mixed, E> $result
             * @return list<E>
             */
            static fn(array $carry, ResultInterface $result) => $result
                ->map(fn() => $carry)
                ->mapError(
                    /**
                     * @param E $t
                     * @return list<E>
                     */
                    fn($t) => array_merge($carry, [ $t ])
                )
                ->get(),
            [],
        );
    }
}
