<?php

declare(strict_types=1);

namespace Someniatko\ResultType;

/**
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
     * @psalm-pure
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
     * @psalm-mutation-free
     * @template TMap
     * @param callable(TValue):TMap $map
     * @return Option<TMap>
     */
    abstract public function map(callable $map): Option;

    /**
     * @psalm-mutation-free
     * @template TMap
     * @param callable(TValue):Option<TMap> $map
     * @return Option<TMap>
     */
    abstract public function flatMap(callable $map): Option;

    /**
     * @psalm-mutation-free
     * @template TElse
     * @param callable():TElse $else
     * @return TValue|TElse
     */
    abstract public function getOr(callable $else);

    /**
     * @psalm-mutation-free
     * @template TElse
     * @param TElse $else
     * @return TValue|TElse
     */
    abstract public function getOrElse($else);

    /**
     * @psalm-mutation-free
     * @param \Throwable $e
     * @return TValue|never-return
     */
    abstract public function getOrThrow(\Throwable $e);

    /**
     * Consumes the Option value into one of the given callbacks.
     * Only one of them will be called, depending on whether it's Some or None.
     *
     * @psalm-suppress InvalidTemplateParam
     * @param callable(TValue):void $ifSome
     * @param callable():void $ifNone
     */
    abstract public function process(callable $ifSome, callable $ifNone): void;

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
     * @psalm-mutation-free
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
     * @psalm-mutation-free
     * @return TValue|null
     */
    public function toNullable()
    {
        return $this->getOrElse(null);
    }
}
