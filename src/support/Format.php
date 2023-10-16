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

    protected int $statusCode = 200;

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
        $this->statusCode = $this->formatStatusCode($this->formatBusinessCode($code), $data);
        return $this->formatDataFields([
            'status'  => $this->formatStatus($this->statusCode),
            'code'    => $this->formatBusinessCode($code),
            'message' => $this->formatMessage($this->formatBusinessCode($code), $message),
            'data'    => $this->formatData($data),
            'error'   => $this->formatError($errors),
        ]);
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
     * @param array $data
     * @return array
     */
    protected function formatDataFields(array $data): array
    {
        return tap($data, function (&$item) use ($data) {
            foreach ($this->config as $key => $config) {
                if (!Arr::has($data, $key)) {
                    continue;
                }

                $show  = $config['show'] ?? true;
                $alias = $config['alias'] ?? '';

                if ($alias && $alias !== $key) {
                    Arr::set($item, $alias, Arr::get($item, $key));
                    $item = Arr::except($item, $key);
                    $key  = $alias;
                }

                if (!$show) {
                    $item = Arr::except($item, $key);
                }
            }
        });
    }

}