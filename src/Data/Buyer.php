<?php
declare(strict_types=1);

namespace App\Data;

class Buyer implements BuyerInterface
{
    public int $country_id;
    public string $country_code;
    public string $country_code3;
    public string $name;
    public string $shop_username;
    public string $email;
    public string $phone;
    public string $address;

    public array $data;

    public function __construct(array $data)
    {
        $this->country_id = (int)($data['country_id'] ?? 0);
        $this->country_code = $data['country_code'] ?? '';
        $this->country_code3 = $data['country_code3'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->shop_username = $data['shop_username'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->phone = $data['phone'] ?? '';
        $this->address = $data['address'] ?? '';
        $this->data = $data['data'] ?? [];
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->data[$offset] ?? null;
    }
}