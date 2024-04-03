<?php

namespace HydrogenpayAfrica\Contract;

interface FactoryInterface
{
    public function create(array $data): Entityinterface;
}
