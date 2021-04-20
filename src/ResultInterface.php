<?php

declare(strict_types=1);

namespace Someniatko\ResultType;

/**
 * @template-covariant TSuccess
 * @template-covariant TError
 * @psalm-immutable
 */
interface ResultInterface
{
    /**
     * Takes a callable which maps **success** value to a new value, returns new ResultInterface.
     * The callable will be called only if this result is Success.
     *
     * If this result is Success, returns new Success with changed value.
     * If this result is Error, returns it as is.
     *
     * @template TNewSuccess
     *
     * @param callable(TSuccess):TNewSuccess $map
     * @return self<TNewSuccess, TError>
     */
    public function map(callable $map): self;

    /**
     * Takes a callable which maps **error** value to a new value, returns new ResultInterface.
     * The callable will be called only if this result is Error.
     *
     * If this result is Success, returns it as is.
     * If this result is Error, returns new Error with changed value.

     * @template TNewError
     *
     * @param callable(TError):TNewError $map
     * @return self<TSuccess, TNewError>
     */
    public function mapError(callable $map): self;

    /**
     * Chains Success path processing. May either just change Success value, or change the result type to Error.
     * Takes a callable which takes **success** value and returns new ResultInterface.
     * The callable will be called only if this result is Success.
     *
     * @template TNewSuccess
     * @template TNewError
     *
     * @param callable(TSuccess):self<TNewSuccess, TNewError> $map
     * @return self<TNewSuccess, TError|TNewError>
     */
    public function chain(callable $map): self;

    /**
     * Returns the final value of this result.
     * The value will be returned for both Success and Error cases.
     *
     * @return TSuccess|TError
     */
    public function get();
}
