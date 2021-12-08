<?php

namespace App\Support\Contracts;

interface ModelInterface
{
    public function create(array $data): array;
    public function update(array $data, $id): array;
    public function delete($id): bool;
    public function all(): array;
    public function find($id): array;
    public function where(array $data): array;
}
