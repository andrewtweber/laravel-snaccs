<?php

namespace Snaccs\Exceptions;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RegistrationDisabledException extends AccessDeniedHttpException
{
}
