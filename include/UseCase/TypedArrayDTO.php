<?php namespace Robust\Boilerplate\UseCase;

/**
 * @template T
 */
class TypedArrayDTO implements \ArrayAccess, \Iterator, \JsonSerializable {
    protected int $position = 0;
    /** @var array<int|string, T> */
    protected array $list = [];

    public function offsetExists($offset): bool {
        return isset($this->list[$offset]);
    }

    /**
     * @return T
     */
    public function offsetGet($offset): mixed {
        return $this->list[$offset];
    }

    /**
     * @param T $value
     */
    public function offsetSet($offset, $value): void {
        if ($offset) {
            $this->list[$offset] = $value;
        } else {
            $this->list[] = $value;
        }
    }

    public function offsetUnset($offset): void {
        unset($this->list[$offset]);
    }

    /**
     * @return array<int|string, T>
     */
    public function jsonSerialize(): array {
        return $this->list;
    }

    /**
     * @return T
     */
    public function current(): mixed {
        return $this->list[$this->position];
    }

    public function next(): void {
        ++$this->position;
    }

    public function key(): int {
        return $this->position;
    }

    public function valid(): bool {
        return isset($this->list[$this->position]);
    }

    public function rewind(): void {
        $this->position = 0;
    }
}