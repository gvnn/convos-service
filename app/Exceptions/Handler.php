<?php namespace App\Exceptions;

use App\Model\ConvosException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
            'error' => ' 500 Internal Server Error',
            'error_description' => 'The server encountered an unexpected condition which prevented it from fulfilling the request.'
        ];

        if ($e instanceof ModelNotFoundException) {
            $status = 404;
            $message = [
                'error' => '404 Not Found',
                'error_description' => 'The server has not found anything matching the Request-URI'
            ];
        } elseif ($e instanceof ConvosException) {
            $status = 400;
            $message = [
                'error' => '400 Bad Request',
                'error_description' => $e->getValidationError()
            ];
        }

        if (env('APP_DEBUG', false)) {
            $message['trace'] = $e->getTrace();
        }

        return response()->json($message, $status);
    }
}

