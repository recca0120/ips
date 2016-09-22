<?php

namespace PayumTW\Ips\Action\Api;

use PayumTW\Ips\Request\Api\CreateTransaction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Reply\HttpPostRedirect;

class CreateTransactionAction extends BaseApiAwareAction
{
    /**
     * {@inheritdoc}
     *
     * @param $request CreateTransaction
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        if ($this->api->isSandbox() === true) {
            throw new HttpPostRedirect(
                $details['Merchanturl'],
                $this->api->generateTestingResponse((array) $details)
            );

            return;
        }

        throw new HttpPostRedirect(
            $this->api->getApiEndpoint(),
            $this->api->createTransaction((array) $details)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof CreateTransaction &&
            $request->getModel() instanceof \ArrayAccess;
    }
}