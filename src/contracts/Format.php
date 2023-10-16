<?php

namespace yuanzhihai\response\think\contracts;

use think\Paginator;

interface Format
{
    /**
     * Format return data structure.
     *
     * @param array|null $data
     * @param string|null $message
     * @param int $code
     * @param null $errors
     * @return array
     */
    public function data(?array $data, ?string $message, int $code, $errors = null): array;

    /**
     * Format paginator data.
     *
     * @param Paginator $resource
     * @return array
     */
    public function paginator(Paginator $resource): array;

}