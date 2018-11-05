<?php

namespace App\Users\Http\Transformers;

use App\Base\Http\Transformer;
use App\Users\User;

class UserTransformer extends Transformer
{
    public function transform(User $user)
    {
        return array_merge(
            $this->id($user),
            [
                'username' => $user->username,
                'email' => $user->email
            ],
            $this->timestamps($user)
        );
    }
}
