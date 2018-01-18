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

namespace Dos\Payum\ToroPay\Action;

use Payum\Core\Exception\UnsupportedApiException;
use Toro\Pay\ToroPay;

trait ApiAwareTrait
{
    /**
     * @var ToroPay
     */
    protected $api;

    /**
     * {@inheritdoc}
     */
    public function setApi($api)
    {
        if (false === $api instanceof ToroPay) {
            throw new UnsupportedApiException(
                sprintf('Not supported api given. It must be an instance of %s', ToroPay::class)
            );
        }

        $this->api = $api;
    }
}
