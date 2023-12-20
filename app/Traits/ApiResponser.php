<?php

namespace App\Traits;

trait ApiResponser
{
    private function success($data, $code)
    {
        return response()->json($data, $code);
    }

    private function error($data, $code = 400)
    {
        if (isset($data['errors'])) {
            $data['message'] = $data['errors']['message'];

            unset($data['errors']);
        }

        return response()->json($data, $code);
    }

    private function resource($resource, $code = 200)
    {
        return $this->success($resource, $code);
    }

    private function collection($collection, $code = 200)
    {
        return $collection;
    }
}
