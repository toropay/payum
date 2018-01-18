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
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Toro\Pay\Domain\Charge;

class StatusAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @param GetStatusInterface $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $status = $model['state'];

        // verify last status
        if ($model['chargeId'] && Charge::STATE_PROCESSING === $status) {
            $charge = $this->api->charge->find($model['chargeId']);
            $status = $model['state'] = $charge->state;

            if (Charge::STATE_FAILED === $status) {
                $model['failureReason'] = $charge->failureReason;
                $this->gateway->execute(new DisplayFailure($model));
            }
        }

        switch ($status) {
            case false:
                $request->markNew();
                break;
            case Charge::STATE_PROCESSING:
                $request->markPending();
                break;
            case Charge::STATE_FINISHED:
                $request->markCaptured();
                break;
            case Charge::STATE_FAILED:
                $request->markFailed();
                break;
            default:
                $request->markUnknown();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
