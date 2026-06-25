<?php

namespace App\Support;

/** Mutable container so filter listeners can return a new value on an event engine. */
class HookValue
{
    public function __construct(public mixed $value) {}
}
