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
            fn (ValidationException $validationException) => response()->json(
                [
                    'status'  => false,
                    'message' => trans('response.failed'),
                    'errors'  => $validationException->errors()
                ],
                status: Response::HTTP_UNPROCESSABLE_ENTITY
            )
        );

        $exceptions->renderable(
            function (NotFoundHttpException $notFoundException) {
                $previousException = $notFoundException->getPrevious();

                if ($previousException instanceof ModelNotFoundException) {
                    return response()->json(
                        [
                            'status'  => false,
                            'message' => trans('response.not_found', [
                                'entity' => str($previousException->getModel())->afterLast('\\')
                            ]),
                        ],
                        status: Response::HTTP_NOT_FOUND
                    );
                }
            }
        );

        $exceptions->renderable(
            fn (MethodNotAllowedHttpException $methodNotAllowedHttpException) => response()->json(
                [
                    'status'  => false,
                    'message' => $methodNotAllowedHttpException->getMessage(),
                ],
                status: Response::HTTP_METHOD_NOT_ALLOWED
            )->setEncodingOptions(JSON_UNESCAPED_SLASHES)
        );
    })->create();
