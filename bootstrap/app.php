<?php

use App\Http\Middleware\UnescapeSlashes;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\{Exceptions, Middleware};
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\{MethodNotAllowedHttpException, NotFoundHttpException};

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('api', [
            'throttle:60,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            UnescapeSlashes::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(
            fn (ValidationException $validationException) => jsonResponse(
                status: false,
                message: trans('response.invalid_paramaters'),
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
                errors: $validationException->errors()
            )
        );

        $exceptions->renderable(
            function (NotFoundHttpException $notFoundException) {
                $previousException = $notFoundException->getPrevious();

                if ($previousException instanceof ModelNotFoundException) {
                    $entity = str($previousException->getModel())->afterLast('\\');

                    return jsonResponse(
                        status: false,
                        message: trans('response.not_found', [
                            'entity' => $entity
                        ]),
                        statusCode: Response::HTTP_NOT_FOUND
                    );
                }
            }
        );

        $exceptions->renderable(
            fn (MethodNotAllowedHttpException $methodNotAllowedHttpException) => jsonResponse(
                status: false,
                message: $methodNotAllowedHttpException->getMessage(),
                statusCode: Response::HTTP_METHOD_NOT_ALLOWED
            )->setEncodingOptions(JSON_UNESCAPED_SLASHES)
        );
    })->create();
