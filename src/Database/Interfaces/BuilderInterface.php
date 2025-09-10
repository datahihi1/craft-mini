<?php
namespace Craft\Database\Interfaces;

interface BuilderInterface {
    public function table(string $table): self;
    public function select($columns = ['*']): self;
    public function where($column, $operator = null, $value = null): self;
    public function insert(array $data): string;
    public function update(array $data): string;
    public function delete(): string;
    public function toSql(): string;
    public function getBindings(): array;
}