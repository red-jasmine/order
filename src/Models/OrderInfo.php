<?php

namespace RedJasmine\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use RedJasmine\Support\Traits\HasDateTimeFormatter;

class OrderInfo extends Model
{

    use HasDateTimeFormatter;

    use SoftDeletes;

    public $incrementing = false;
}
