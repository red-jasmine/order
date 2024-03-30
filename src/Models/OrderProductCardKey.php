<?php

namespace RedJasmine\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use RedJasmine\Support\Traits\HasDateTimeFormatter;
use RedJasmine\Support\Traits\Models\HasOperator;

class OrderProductCardKey extends Model
{


    use HasDateTimeFormatter;

    use HasTradeParties;

    use HasOperator;

    use SoftDeletes;
}
