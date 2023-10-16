<?php

namespace yuanzhihai\response\think\support;

use Spatie\Macroable\Macroable;
use think\contract\Arrayable;
use think\facade\Config;
use think\facade\Lang;
use think\helper\Arr;
use think\Paginator;
use think\Response;

class Format implements \yuanzhihai\response\think\contracts\Format
{
    use Macroable;

    protected $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * Return a new JSON response from the application.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @param null $errors
     * @param array $headers
     * @param array $options
     * @param string $from
     * @return Response
     */
    public function response($data = [], string $message = '', int $code = 200, $errors = null, array $headers = [], array $options = [], string $from = 'success'): Response
    {
        return Response::create($this->data($data, $message, $code, $errors), 'json', $this->formatStatusCode($code, $from))->header($headers)->options($options);
    }

    /**
     * Format return data structure.
     *
     * @param array|null $data
     * @param string|null $message
     * @param int $code
     * @param null $errors
     * @return array
     */
    public function data($data, ?string $message, int $code, $errors = null): array
    {
        return $this->formatDataFields([
            'status'  => $this->formatStatus($this->formatStatusCode($code, $data)),
            'code'    => $code,
            'message' => $this->formatMessage($code, $message),
            'data'    => $this->formatData($data),
            'error'   => $this->formatError($errors),
        ], $this->config);
    }

    /**
     * Format paginator data.
     *
     * @param Paginator $resource
     * @return array
     */
    public function paginator(Paginator $resource): array
    {
        return [
            'data' => $resource->toArray()['data'],
            'meta' => $this->formatPaginatedData($resource),
        ];
    }


    /**
     * Format return message.
     *
     * @param int $code
     * @param string|null $message
     * @return string
     */
    protected function formatMessage(int $code, ?string $message): ?string
    {
        $localizationKey = implode('.', [Config::get('response.locale', 'enums'), $code]);
        return match (true) {
            !$message && Lang::has($localizationKey) => Lang::get($localizationKey),
            default => $message
        };

    }

    /**
     * Format http status description.
     *
     * @param int $statusCode
     * @return string
     */
    protected function formatStatus(int $statusCode): string
    {
        return match (true) {
            ($statusCode >= 400 && $statusCode <= 499) => 'error',// client error
            ($statusCode >= 500 && $statusCode <= 599) => 'fail',// service error
            default => 'success'
        };

    }

    /**
     * Http status code.
     * @return int
     */
    protected function formatStatusCode($code, $oriData): int
    {
        return (int)substr(is_null($oriData) ? (Config::get('response.error_code') ?: $code) : $code, 0, 3);
    }

    /**
     * Format data.
     */
    protected function formatData($data): array|object
    {
        return match (true) {
            $data instanceof Paginator => $this->paginator($data),
            $data instanceof Arrayable || (is_object($data) && method_exists($data, 'toArray')) => $data->toArray(),
            empty($data) => (object)$data,
            default => Arr::wrap($data)
        };
    }

    /**
     * Format paginated data.
     *
     * @param $collection
     * @return array
     */
    protected function formatPaginatedData($collection): array
    {
        return match (true) {
            $collection instanceof Paginator => [
                'pagination' => [
                    'count'        => $collection->listRows(),
                    'per_page'     => $collection->lastPage(),
                    'current_page' => $collection->currentPage(),
                    'total'        => $collection->total(),
                ],
            ],
            default => [],
        };
    }

    /**
     * Format error.
     */
    protected function formatError(?array $error): object|array
    {
        return $error ?: (object)[];
    }

    /**
     * Format response data fields.
     *
     * @param array $responseData
     * @param array $dataFieldsConfig
     * @return array
     */
    protected function formatDataFields(array $responseData, array $dataFieldsConfig = []): array
    {
        if (empty($dataFieldsConfig)) {
            return $responseData;
        }

        foreach ($responseData as $field => $value) {
            $fieldConfig = Arr::get($dataFieldsConfig, $field);
            if (is_null($fieldConfig)) {
                continue;
            }

            if ($value && is_array($value) && in_array($field, ['data', 'meta', 'pagination'])) {
                $value = $this->formatDataFields($value, Arr::get($dataFieldsConfig, "{$field}.fields", []));
            }

            $alias = $fieldConfig['alias'] ?? $field;
            $show  = $fieldConfig['show'] ?? true;
            $map   = $fieldConfig['map'] ?? null;
            unset($responseData[$field]);

            if ($show) {
                $responseData[$alias] = $map[$value] ?? $value;
            }
        }

        return $responseData;
    }

}