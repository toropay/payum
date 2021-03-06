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

namespace Toro\Payum;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory as PayumGatewayFactory;
use Toro\Pay\ToroPay;
use Toro\Payum\Action\AuthorizeAction;
use Toro\Payum\Action\CaptureAction;
use Toro\Payum\Action\ConvertPaymentAction;
use Toro\Payum\Action\StatusAction;

class GatewayFactory extends PayumGatewayFactory
{
    /**
     * {@inheritdoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => ToroPay::SERVICE_NAME,
            'payum.factory_title' => ucfirst(ToroPay::SERVICE_NAME),
            'payum.action.status' => new StatusAction(),
            'payum.action.authorize' => new AuthorizeAction(),
            'payum.action.capture' => new CaptureAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
        ]);

        if (!$config['payum.api.toropay-api']) {
            $config['payum.api'] = function (ArrayObject $config) {
                return new ToroPay([
                    'clientId' => $config['toropay_client_id'],
                    'clientSecret' => $config['toropay_client_secret'],
                    'redirectUri' => $config['toropay_redirect_uri'],
                    'ownerProvider' => $config['toropay_owner_provider'],
                    'sandbox' => $config['toropay_sandbox'],
                ]);
            };
        }
    }
}
