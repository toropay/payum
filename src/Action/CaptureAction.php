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

namespace Dos\Payum\Action;

use Dos\Payum\Request\DisplayFailure;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Authorize;
use Payum\Core\Request\Capture;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetHttpRequest;
use Toro\Pay\Domain\Charge;
use Toro\Pay\Exception\InvalidResponseException;

class CaptureAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    /**
     * @param Capture $request
     *
     * @throws \Payum\Core\Reply\ReplyInterface
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        //$this->gateway->execute($httpRequest = new GetHttpRequest());

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if ($securityToken = $request->getToken()) {
            $model['returnUri'] = $securityToken->getAfterUrl();
        }

        $charge = new Charge();
        $charge->amount = $model['amount'];
        $charge->note = $model['note'];
        $charge->returnUri = $model['returnUri'];
        //$charge->currency = $model['currency'];

        try {
            $this->api->charge->createNew($charge);
        } catch (InvalidResponseException $e) {
            $model['failureReason'] = $e->error->message;

            $this->gateway->execute(new DisplayFailure($model));

            return;
        }

        // 3D-Secure
        if ($charge->authorizeUri) {
            $model['authorizeUri'] = $charge->authorizeUri;

            $this->gateway->execute(new Authorize($model));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
