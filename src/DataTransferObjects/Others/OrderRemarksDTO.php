<?php

namespace RedJasmine\Order\DataTransferObjects\Others;

use RedJasmine\Order\Enums\Others\RemarkFormEnum;
use RedJasmine\Support\DataTransferObjects\Data;

class OrderRemarksDTO extends Data
{


    public RemarkFormEnum $form;

    public ?string $remarks;
}
