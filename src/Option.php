<?php

declare(strict_types=1);

namespace Someniatko\ResultType;

/**
 * @psalm-immutable
 * @template-covariant TValue
 */
abstract class Option
{
    /** @var None|null */
    private static ?None $none = null;

    /**
     * @psalm-pure
     * @template TNew
     * @param TNew $value
     * @return Some<TNew>
     */
    public static function some($value): Some
    {
        return new Some($value);
    }

    /**
     * @psalm-pure
     * @return None
     */
    public static function none(): None
    {
        /** @psalm-suppress ImpureStaticProperty */
        return self::$none ??= new None();
    }

    /**
     * @template TNew
     * @param TNew|null $value
     * @return Option<TNew>
     */
    public static function fromNullable($value): Option
    {
        return $value === null
            ? self::none()
            : self::some($value);
    }

    /**
     * @template T
     * @param list<Option<T>> $options
     * @return Option<list<T>>
     */
    public static function all(array $options): Option
    {
        /**
         * @psalm-suppress InvalidArgument
         * @psalm-suppress MixedArgumentTypeCoercion
         * https://github.com/vimeo/psalm/issues/8342
         */
        return array_reduce(
            $options,
            /**
             * @param Option<list<T>> $carry
             * @param Option<T> $o
             * @return Option<list<T>>
             */
            fn(Option $carry, Option $o) => $carry->flatMap(
                /**
                 * @param list<T> $ts
                 * @return Option<list<T>>
                 */
                fn(array $ts) => $o->map(
                    /**
                     * @param T $t
                     * @return list<T>
                     */
                    fn($t) => array_merge($ts, [ $t ])
                )
            ),
            new Some([]),
        );
    }

    /**
     * @template TMap
     * @param callable(TValue):TMap $map
     * @return Option<TMap>
     */
    abstract public function map(callable $map): Option;

    /**
     * @template TMap
     * @param callable(TValue):Option<TMap> $map
     * @return Option<TMap>
     */
    abstract public function flatMap(callable $map): Option;

    /**
     * @template TElse
     * @param callable():TElse $else
     * @return TValue|TElse
     */
    abstract public function getOr(callable $else);

    /**
     * @template TElse
     * @param TElse $else
     * @return TValue|TElse
     */
    abstract public function getOrElse($else);

    /**
     * @param \Throwable $e
     * @return TValue|never-return
     */
    abstract public function getOrThrow(\Throwable $e);

    /**
     * Ensures that Some value also validates against the given condition,
     * Otherwise returns None.
     *
     * @return Option<TValue>
     *
     * @param callable(TValue):bool $condition
     */
    abstract public function ensure(callable $condition): Option;

    /**
     * @template TElse
     * @param TElse $else
     * @return ResultInterface<TValue, TElse>
     */
    public function toResult($else): ResultInterface
    {
        return $this
            ->map(fn($t) => new Success($t))
            ->getOr(fn() => new Error($else));
    }

    /**
     * @template TElse
     * @param callable():TElse $else
     * @return ResultInterface<TValue, TElse>
     */
    public function toResultLazy(callable $else): ResultInterface
    {
        return $this
            ->map(fn($t) => new Success($t))
            ->getOr(fn() => new Error($else()));
    }

    /**
     * @return TValue|null
     */
    public function toNullable()
    {
        return $this->getOrElse(null);
    }
}
