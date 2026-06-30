<?php

declare(strict_types=1);

namespace Medas\RestRequestHandlerGenerator\Exceptions;

use Medas\Core\Exceptions\BaseException;

class NoClosingBraceFound extends BaseException
{
    public function pattern(): string
    {
        return 'Could not find a closing brace to insert the generated method before';
    }
}
