<?php

namespace HydrogenAfrica\Contract;

interface FactoryInterface
{
    public function create(array $data): Entityinterface;
}
