<?php

namespace App\Base\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

use App\Common\Response;
use App\Base\Http\Serializer;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $response;

    public function __construct()
    {
        $this->response = new Response;
    }

    /**
     * Transform a model or collection of models
     */
    protected function transform($data, $transformer)
    {
        if ($data instanceof Model) {
            $fractalData = new Item($data, $transformer);
        } else {
            // Should be a collection
            $fractalData = new Collection($data, $transformer);
        }
        $fractal = new Manager;
        $fractal->setSerializer(new Serializer);
        $scope = $fractal->createData($fractalData);
        $ret = $scope->toArray();
        return $ret;
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     */
    public function _validate(Request $request, array $rules,
                             array $messages = [], array $customAttributes = [])
    {
        $validator = $this->getValidationFactory()
             ->make($request->all(), $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return false;
    }
}
