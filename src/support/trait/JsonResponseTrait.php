<?php

namespace yuanzhihai\response\think\support\trait;

use think\Response;

trait JsonResponseTrait
{
    /**
     *  Respond with an accepted response and associate a location and/or content if provided.
     *
     * @param array $data
     * @param string $message
     * @param string $location
     * @return Response
     */
    public function accepted($data = [], string $message = '', string $location = '')
    {
        $response = $this->success($data, $message, 202);
        if ($location) {
            $response->header(['Location' => $location]);
        }

        return $response;
    }

    /**
     * Respond with a created response and associate a location if provided.
     *
     * @param null $data
     * @param string $message
     * @param string $location
     * @return Response
     */
    public function created($data = [], string $message = '', string $location = '')
    {
        $response = $this->success($data, $message, 201);
        if ($location) {
            $response->header(['Location' => $location]);
        }

        return $response;
    }

    /**
     * Respond with a no content response.
     *
     * @param string $message
     * @return Response
     */
    public function noContent(string $message = '')
    {
        return $this->success([], $message, 204);
    }

    /**
     * Alias of success method, no need to specify data parameter.
     *
     * @param string $message
     * @param int $code
     * @param array $headers
     * @param array $options
     * @return Response
     */
    public function ok(string $message = '', int $code = 200, array $headers = [], array $options = [])
    {
        return $this->success([], $message, $code, $headers, $options);
    }

    /**
     * Alias of the successful method, no need to specify the message and data parameters.
     * You can use ResponseCodeEnum to localize the message.
     *
     * @param int $code
     * @param array $headers
     * @param array $options
     * @return Response
     */
    public function localize(int $code = 200, array $headers = [], array $options = [])
    {
        return $this->ok('', $code, $headers, $options);
    }

    /**
     * Return a 400 bad request error.
     *
     * @param string|null $message
     */
    public function errorBadRequest(string $message = '')
    {
        return $this->fail($message, 400);
    }

    /**
     * Return a 401 unauthorized error.
     *
     * @param string $message
     */
    public function errorUnauthorized(string $message = '')
    {
        return $this->fail($message, 401);
    }

    /**
     * Return a 403 forbidden error.
     *
     * @param string $message
     */
    public function errorForbidden(string $message = '')
    {
        return $this->fail($message, 403);
    }

    /**
     * Return a 404 not found error.
     *
     * @param string $message
     */
    public function errorNotFound(string $message = '')
    {
        return $this->fail($message, 404);
    }

    /**
     * Return a 405 method not allowed error.
     *
     * @param string $message
     */
    public function errorMethodNotAllowed(string $message = '')
    {
        return $this->fail($message, 405);
    }

    /**
     * Return a 500 internal server error.
     *
     * @param string $message
     */
    public function errorInternal(string $message = '')
    {
        return $this->fail($message);
    }

    /**
     * Return an fail response.
     *
     * @param string $message
     * @param int $code
     * @param array|null $errors
     * @param array $headers
     * @param array $options
     * @return Response
     *
     */
    public function fail(string $message = '', int $code = 500, $errors = null, array $headers = [], array $options = [])
    {
        return $this->formatter->response(null, $message, $code, $errors, $headers, $options, 'fail');
    }

    /**
     * Return a success response.
     *
     * @param array|mixed $data
     * @param string $message
     * @param int $code
     * @param array $headers
     * @param array $options
     * @return Response
     */
    public function success($data = [], string $message = '', int $code = 200, array $headers = [], array $options = []): Response
    {
        return $this->formatter->response(...func_get_args());
    }
}