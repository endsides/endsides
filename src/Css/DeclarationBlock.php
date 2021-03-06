<?php

namespace Endsides\Css;

class DeclarationBlock implements \ArrayAccess, \IteratorAggregate, \Countable {
	public function __construct(Declaration ...$declarations) {
		$this->setDeclarations($declarations);
	}

	protected array $declarations = [];

	public function setDeclarations(array $declarations): self {
		return $this->resetDeclarations()->addDeclarations($declarations);
	}

	public function resetDeclarations(): self {
		$this->declarations = [];
		return $this;
	}

	public function addDeclarations(array $declarations): self {
		foreach ($declarations as $declaration) {
			$this->addDeclaration($declaration);
		}
		return $this;
	}

	public function addDeclaration(Declaration $declaration): self {
		$this->declarations[] = $declaration;
		return $this;
	}

	public function getDeclarations(array $properties = []): array {
		if (empty($properties)) {
			return $this->declarations;
		}

		$declarations = [];
		foreach ($properties as $property) {
			if ($declaration = $this->getDeclaration($property)) {
				$declarations[] = $declaration;
			}
		}
		return $declarations;
	}

	public function hasDeclarations(): bool {
		return !empty($this->getDeclarations());
	}

	public function getDeclaration(string $property): ?Declaration {
		foreach ($this->getDeclarations() as $declaration) {
			if ($declaration->getProperty()->nameIs($property)) {
				return $declaration;
			}
		}
		return null;
	}

	public function hasDeclaration(string $property): bool {
		return !is_null($this->getDeclaration($property));
	}

	public function removeDeclaration(string $property): self {
		if ($declaration = $this->getDeclaration($property)) {
			$key = array_search($declaration, $this->declarations);
			array_splice($this->declarations, $key, 1);
		}
		return $this;
	}

	public function removeDeclarations(array $properties): self {
		foreach ($properties as $property) {
			$this->removeDeclaration($property);
		}
		return $this;
	}

	public function getIterator(): \ArrayIterator {
		return new \ArrayIterator($this->getDeclarations());
	}

	public function count(): int {
		return count($this->getDeclarations());
	}

	public function offsetExists($offset): bool {
		return $this->hasDeclaration($offset);
	}

	public function offsetGet($offset): ?Declaration {
		return $this->getDeclaration($offset);
	}

	public function offsetSet($offset, $value): void {
		$this->addDeclaration($value);
	}

	public function offsetUnset($offset): void {
		$this->removeDeclaration($offset);
	}
}
