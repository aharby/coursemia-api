<?php

namespace App\OurEdu\BaseApp\Api\Traits;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use Throwable;
use Spatie\Fractal\Facades\Fractal;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

trait ApiResponser
{
    protected function successResponse($data, $code = 200)
    {
        return response()->json($data, $code);
    }

    protected function errorResponse($message, $code = 500)
    {
        return response()->json([
            'error' => $message, 'code' => $code
        ], $code);
    }

    protected function jsonApiException($exception, $code = 500)
    {
        return [
            [
                'status' => $code,
                'title' => snake_case($exception->getMessage()),
                'detail' => $exception->getTrace()
            ]
        ];
    }

    protected function showOne(?Model $instance, $code = 200, $transformer = null, $resourceName = null)
    {
        $return = $this->transformDataMod($instance, $transformer, $resourceName);
        return $this->successResponse($return, $code);
    }

    protected function transformDataMod($data, $transformer, $resourceName = null)
    {
        if (empty($data)) {
            return $this->successResponse(['data' => []]);
        }
        return fractal($data, new $transformer)
            ->serializeWith(new JsonApiSerializer())
            ->withResourceName($resourceName)
            ->toArray();
    }

    protected function transformDataModInclude($data, $include, $transformer, $resourceName = null, $meta = [])
    {
        try {
            if (empty($data) && empty($meta)) {
                return $this->successResponse(['data' => []]);
            } elseif (empty($data) && count($meta) > 0) {
                return fractal(null, $transformer)
                    ->withResourceName($resourceName)
                    ->parseIncludes($include)
                    ->addMeta($meta)
                    ->toArray();
            } else {
//                new Fractal()

//              return  Fractal::Item($data,$transformer,'resource_name')->serializeWith(new JsonApiSerializer())->addMeta($meta)->toArray();

//                $manager = new Manager();
//
//                $resource = new Item($data, $transformer);
//
//                $manager->parseIncludes($include);
//                $manager->meta($meta);
//
//                $manager->setSerializer(new JsonApiSerializer());
//              return  $manager->createData($resource)->toArray();


                return fractal($data, $transformer)

                    ->serializeWith(new JsonApiSerializer())
                    ->parseIncludes($include)
                    ->addMeta($meta)
                    ->withResourceName($resourceName)

                    ->toArray();
            }
        } catch (Throwable $e) {
            return $this->jsonApiException($e);
        }
    }

    protected function transformDataModIncludeItem($data, $include, $transformer, $resourceName = null, $meta = [])
    {
        try {
            if (empty($data) && empty($meta)) {
                return $this->successResponse(['data' => []]);
            } elseif (empty($data) && count($meta) > 0) {
                return fractal(null, $transformer)
                    ->withResourceName($resourceName)
                    ->parseIncludes($include)
                    ->addMeta($meta)
                    ->toArray();
            } else {
//                new Fractal()

//              return  Fractal::Item($data,$transformer,'resource_name')->serializeWith(new JsonApiSerializer())->addMeta($meta)->toArray();

//                $manager = new Manager();
//
//                $resource = new Item($data, $transformer);
//
//                $manager->parseIncludes($include);
//                $manager->meta($meta);
//
//                $manager->setSerializer(new JsonApiSerializer());
//              return  $manager->createData($resource)->toArray();


                return fractal($data, $transformer)

                    ->serializeWith(new JsonApiSerializer())
                    ->parseIncludes($include)
                    ->addMeta($meta)
                    ->withResourceName($resourceName)

                    ->toArray();
            }
        } catch (Throwable $e) {
            return $this->jsonApiException($e);
        }
    }
}
