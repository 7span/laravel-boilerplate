<?php

namespace App\OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Attributes as OA;

class AddPaginationProcessor
{
    public function __invoke(Analysis $analysis): void
    {
        // dd($analysis->openapi->paths);
        foreach ($analysis->openapi->paths as $path) {
            $operation = $this->findAssociatedOperation($analysis, $path->_context);
            foreach ($operation->_context->annotations as $annotation) {
                $context = $annotation->_context;
                $operationNested = $this->findAssociatedOperation($analysis, $context);
                $responseKeys = array_keys($operationNested->responses);
                if (!in_array(200, $responseKeys)) {
                    $operationNested->responses[200] = new OA\Response(response: 200, description: 'Success');
                }
            }
        }



        // foreach ($analysis->openapi->paths as $path) {
        //     foreach ($path->getOperations() as $operation) {
        //         // Only for GET requests
        //         if (strtolower($operation->method) === 'get') {
        //             $operation->parameters = array_merge(
        //                 $operation->parameters ?? [],
        //                 [
        //                     new OA\Parameter(
        //                         name: "page",
        //                         in: "query",
        //                         description: "Page number (starts at 1)",
        //                         required: false,
        //                         schema: new OA\Schema(type: "integer", default: 1, minimum: 1)
        //                     ),
        //                     new OA\Parameter(
        //                         name: "limit",
        //                         in: "query",
        //                         description: "Items per page",
        //                         required: false,
        //                         schema: new OA\Schema(type: "integer", default: 20, minimum: 1, maximum: 100)
        //                     )
        //                 ]
        //             );
        //         }
        //     }
        // }
    }

    private function findAssociatedOperation(Analysis $analysis, $context)
    {
        $operations = [
            OA\Get::class,
            OA\Post::class,
            OA\Put::class,
            OA\Delete::class,
            OA\Patch::class,
            OA\Head::class,
            OA\Options::class,
        ];

        foreach ($operations as $operationClass) {
            foreach ($analysis->getAnnotationsOfType($operationClass) as $operation) {
                if (
                    $operation->_context &&
                    $operation->_context->method === $context->method &&
                    $operation->_context->class === $context->class
                ) {
                    return $operation;
                }
            }
        }

        return null;
    }
}
