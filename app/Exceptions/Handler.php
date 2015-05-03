<?php namespace App\Exceptions;

use App\Model\ConvosException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException'
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $status = 500;
        $message = [
            'status_code' => $status,
            'error_description' => 'Internal Server Error'
        ];

        if ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();
            $message = [
                'status_code' => $e->getStatusCode()
            ];
        } elseif ($e instanceof ModelNotFoundException) {
            $status = 404;
            $message = [
                'status_code' => $status,
                'error_description' => 'Item not found'
            ];
        } elseif ($e instanceof ConvosException) {
            $status = 400;
            $message = [
                'status_code' => $status,
                'error_description' => $e->getValidationError()
            ];
        }

        if (env('APP_DEBUG', false)) {
            $message['trace'] = $e->getTrace();
        }

        return response()->json($message, $status);
    }
}

