<?php

namespace App\Base\Http;

use League\Fractal\Serializer\ArraySerializer;

/**
 * Outputs the format of a transformer
 * Overrides default of putting all items in data key
 */
class Serializer extends ArraySerializer
{
    public function collection($resourceKey, array $data)
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }

        return $data;
    }

    public function item($resourceKey, array $data)
    {
        if ($resourceKey) {
            return [$resourceKey => $data];
        }
        return $data;
    }
}
