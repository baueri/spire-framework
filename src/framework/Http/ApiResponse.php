<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Http;

class ApiResponse
{
    public function ok($data = null): array
    {
        return $this->response($data, true);
    }

    public function error($data = [], ?int $code = null): array
    {
        return $this->response($data, false, $code);
    }

    public function response($data, ?bool $success = null, ?int $code = null): array
    {
        Response::asJson();

        if (is_bool($data)) {
            $success = $data;
            $data = [];
        } elseif (is_string($data)) {
            $data = ['msg' => $data];
        }

        if ($code) {
            Response::setStatusCode($code);
        }

        return array_merge(compact('success'), $data ?? []);
    }
}
