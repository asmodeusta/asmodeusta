<?php

namespace Usf\Base;

use Usf\Base\Traits\DataTrait;
use Usf\Base\Traits\Observable;

class UsfView
{

    use DataTrait;

    use Observable;

    protected $template;

    public function __construct()
    {

    }

}