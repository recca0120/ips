<?php

namespace PayumTW\Ips\Tests\Action;

use Mockery as m;
use Payum\Core\Request\Notify;
use PHPUnit\Framework\TestCase;
use Payum\Core\Reply\ReplyInterface;
use PayumTW\Ips\Action\NotifyAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\GetHttpRequest;

class NotifyActionTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testExecute()
    {
        $action = new NotifyAction();
        $request = new Notify(new ArrayObject([]));

        $action->setGateway(
            $gateway = m::mock('Payum\Core\GatewayInterface')
        );

        $response = [
            'paymentResult' => 'foo',
        ];

        $gateway->shouldReceive('execute')->once()->with(m::on(function ($httpRequest) use ($response) {
            $httpRequest->request = $response;

            return $httpRequest instanceof GetHttpRequest;
        }));

        $action->setApi(
            $api = m::mock('PayumTW\Ips\Api')
        );

        $api->shouldReceive('parseResponse')->once()->with($response)->andReturn($params = ['foo' => 'bar']);
        $api->shouldReceive('verifyHash')->once()->with($params)->andReturn(true);

        try {
            $action->execute($request);
        } catch (ReplyInterface $e) {
            $this->assertSame(200, $e->getStatusCode());
            $this->assertSame('1', $e->getContent());
        }

        $this->assertSame($params, (array) $request->getModel());
    }

    public function testExecuteFail()
    {
        $action = new NotifyAction();
        $request = new Notify(new ArrayObject([]));

        $action->setGateway(
            $gateway = m::mock('Payum\Core\GatewayInterface')
        );

        $response = [
            'paymentResult' => 'foo',
        ];

        $gateway->shouldReceive('execute')->once()->with(m::on(function ($httpRequest) use ($response) {
            $httpRequest->request = $response;

            return $httpRequest instanceof GetHttpRequest;
        }));

        $action->setApi(
            $api = m::mock('PayumTW\Ips\Api')
        );

        $api->shouldReceive('parseResponse')->once()->with($response)->andReturn($params = ['foo' => 'bar']);
        $api->shouldReceive('verifyHash')->once()->with($params)->andReturn(false);

        try {
            $action->execute($request);
        } catch (ReplyInterface $e) {
            $this->assertSame(400, $e->getStatusCode());
            $this->assertSame('Signature verify fail.', $e->getContent());
        }
    }
}
