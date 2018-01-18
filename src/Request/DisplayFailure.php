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

class DisplayFailure extends Generic
{
    /**
     * @var string
     */
    private $failureReason;

    public function __construct($model)
    {
        parent::__construct($model);

        $this->failureReason = $model['failureReason'];
    }

    /**
     * @return string
     */
    public function getFailureReason(): string
    {
        return $this->failureReason;
    }
}
