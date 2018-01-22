<?php

/*
 * This file is part of the Doss package.
 *
 * (c) Ishmael Doss <nukboon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Toro\Payum\Request;

use Payum\Core\Request\Generic;
use Toro\Pay\Domain\Error;

class DisplayFailure extends Generic
{
    /**
     * @return string
     */
    public function getFailureCode(): string
    {
        return (string)$this->getError()->code;
    }

    /**
     * @return string
     */
    public function getFailureReason(): string
    {
        return $this->getError()->message;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->getError()->errors;
    }

    /**
     * @return Error
     */
    public function getError(): Error
    {
        return $this->model['error'];
    }
}
