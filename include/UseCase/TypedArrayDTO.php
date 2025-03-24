<?php namespace Robust\Boilerplate\UseCase;

class TypedArrayDTO implements \ArrayAccess, \Iterator, \JsonSerializable {
    protected int $position = 0;
    protected array $list = [];

    public function offsetExists($offset): bool {
        return isset($this->list[$offset]);
    }

    public function offsetGet($offset): mixed {
        return $this->list[$offset];
    }

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

    public function jsonSerialize(): array {
        return $this->list;
    }

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