<?php

namespace Usf\Base\Interfaces;

interface AuthorizationInterface
{

    public function checkRequiredFields();

    public function validate();

    public function checkAuthorization();

}