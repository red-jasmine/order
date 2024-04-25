<?php

namespace RedJasmine\Order\Tests\Users;

use RedJasmine\Support\Contracts\UserInterface;

class User extends \Illuminate\Foundation\Auth\User implements UserInterface
{


    protected $fillable = [
        'id'
    ];

    public static function make(int $id) : static
    {
        return new static([ 'id' => $id ]);
    }

    public function getType() : string
    {
        return 'buyer';
    }

    public function getID() : int
    {
        return $this->getKey();
    }

    public function getNickname() : ?string
    {
        return 'user';
    }

    public function getAvatar() : ?string
    {
        return null;
    }


}
