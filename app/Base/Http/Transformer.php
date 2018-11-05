<?php

namespace App\Base\Http;

use League\Fractal\TransformerAbstract;

class Transformer extends TransformerAbstract
{
    protected function id($model)
    {
        return [
            'id' => (int) $model->id
        ];
    }

    protected function timestamps($model)
    {
        return [
            'created_at' => (string) $model->created_at,
            'updated_at' => (string) $model->updated_at
        ];
    }
}
