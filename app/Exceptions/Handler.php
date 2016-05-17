<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     *
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $response = [
            'message' => (string) $e->getMessage(),
        ];

        if ($e instanceof ValidationException) {
            $errors = [];
            $types = $e->validator->failed();
            foreach ($types as $field => $type) {
                $errors[] = [
                    'field' => $field,
                    'code' => Str::snake(current(array_keys($type))),
                    'message' => $e->validator->messages()->first($field)
                ];
            }
            $response['errors'] = $errors;
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        } else if ($e instanceof QueryException) {
            $response['message'] = 'Could\'nt Create/Update';
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        } else if ($e instanceof \PDOException) {
            $response['message'] = 'DB Connection Error';
            return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        } else if ($e instanceof HttpException) {
            $response['message'] = Response::$statusTexts[$e->getStatusCode()];
            return response()->json($response, $e->getStatusCode());
        } else if ($e instanceof ModelNotFoundException) {
            $response['message'] = 'Not Found';
            return response()->json($response, Response::HTTP_NOT_FOUND);
        } else {
            if ($_ENV['APP_DEBUG']) {
                return parent::render($request, $e);
            }
            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
