<?php

namespace PayumTW\Ips\Tests;

use Mockery as m;
use PayumTW\Ips\Api;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testCreateTransaction()
    {
        $api = new Api(
            $options = [
                'Version' => 'v1.0.0',
                'MerCode' => 'c5374addd1a3d024c3a026199cb8feaf',
                'MerKey' => 'c71530d016b3475579da8af971f7ca6c',
                'MerName' => null,
                'Account' => 'b02e072eee68d65bff916e08b4f11df2',
                'sandbox' => false,
            ],
            $httpClient = m::mock('Payum\Core\HttpClientInterface'),
            $message = m::mock('Http\Message\MessageFactory')
        );

        $this->assertSame([
            'pGateWayReq' => '<Ips><GateWayReq><head><Version>v1.0.0</Version><MerCode>c5374addd1a3d024c3a026199cb8feaf</MerCode><Account>b02e072eee68d65bff916e08b4f11df2</Account><ReqDate>20160903021801</ReqDate><Signature>527c01c2b04d4f8b198180ba72b0f66e</Signature></head><body><MerBillNo>57c9aca80fdb4</MerBillNo><GatewayType>01</GatewayType><Date>20160903</Date><CurrencyType>156</CurrencyType><Amount>0.01</Amount><Lang>GB</Lang><Merchanturl><![CDATA[http://localhost/baotao/public/payment/capture/PHEzq0mjdqXM8EH7TQ3jDL6ONJMnBkt85BVrzK2TbJU]]></Merchanturl><FailUrl><![CDATA[http://localhost/baotao/public/payment/capture/PHEzq0mjdqXM8EH7TQ3jDL6ONJMnBkt85BVrzK2TbJU]]></FailUrl><OrderEncodeType>5</OrderEncodeType><RetEncodeType>17</RetEncodeType><RetType>1</RetType><ServerUrl><![CDATA[http://localhost/baotao/public/payment/notify/Fz2WIYvQs1KPc3tRUGNKtOnkHFZuF4segpRnZdphze8]]></ServerUrl><GoodsName><![CDATA[商品名稱]]></GoodsName></body></GateWayReq></Ips>',
        ], $api->createTransaction($params = [
            'ReqDate' => '20160903021801',
            'MerBillNo' => '57c9aca80fdb4',
            'GatewayType' => '01',
            'Date' => '20160903',
            'CurrencyType' => 156,
            'Amount' => 0.01,
            'Lang' => 'GB',
            'Merchanturl' => 'http://localhost/baotao/public/payment/capture/PHEzq0mjdqXM8EH7TQ3jDL6ONJMnBkt85BVrzK2TbJU',
            'FailUrl' => 'http://localhost/baotao/public/payment/capture/PHEzq0mjdqXM8EH7TQ3jDL6ONJMnBkt85BVrzK2TbJU',
            'OrderEncodeType' => 5,
            'RetEncodeType' => 17,
            'RetType' => 1,
            'ServerUrl' => 'http://localhost/baotao/public/payment/notify/Fz2WIYvQs1KPc3tRUGNKtOnkHFZuF4segpRnZdphze8',
            'GoodsName' => '商品名稱',
        ]));
    }

    public function testParseResponse()
    {
        $api = new Api(
            $options = [
                'Version' => 'v1.0.0',
                'MerCode' => 'c5374addd1a3d024c3a026199cb8feaf',
                'MerKey' => 'c71530d016b3475579da8af971f7ca6c',
                'MerName' => null,
                'Account' => 'b02e072eee68d65bff916e08b4f11df2',
                'sandbox' => false,
            ],
            $httpClient = m::mock('Payum\Core\HttpClientInterface'),
            $message = m::mock('Http\Message\MessageFactory')
        );

        $paymentResult = '<Ips><GateWayRsp><head><ReferenceID></ReferenceID><RspCode>000000</RspCode><RspMsg><![CDATA[交易成功！]]></RspMsg><ReqDate>20160903022511</ReqDate><RspDate>20160903022558</RspDate><Signature>598633a6fcae5562ef63355f12a71ee1</Signature></head><body><MerBillNo>57c9aca80fdb4</MerBillNo><CurrencyType>156</CurrencyType><Amount>0.01</Amount><Date>20160903</Date><Status>Y</Status><Msg><![CDATA[支付成功！]]></Msg><IpsBillNo>BO20160903020025003799</IpsBillNo><IpsTradeNo>2016090302091180230</IpsTradeNo><RetEncodeType>17</RetEncodeType><BankBillNo>710002875951</BankBillNo><ResultType>0</ResultType><IpsBillTime>20160903022542</IpsBillTime></body></GateWayRsp></Ips>';

        $this->assertSame([
            'paymentResult' => $paymentResult,
            'ReferenceID' => '',
            'RspCode' => '000000',
            'RspMsg' => '交易成功！',
            'ReqDate' => '20160903022511',
            'RspDate' => '20160903022558',
            'Signature' => '598633a6fcae5562ef63355f12a71ee1',
            'MerBillNo' => '57c9aca80fdb4',
            'CurrencyType' => '156',
            'Amount' => '0.01',
            'Date' => '20160903',
            'Status' => 'Y',
            'Msg' => '支付成功！',
            'IpsBillNo' => 'BO20160903020025003799',
            'IpsTradeNo' => '2016090302091180230',
            'RetEncodeType' => '17',
            'BankBillNo' => '710002875951',
            'ResultType' => '0',
            'IpsBillTime' => '20160903022542',
            'Signature' => '598633a6fcae5562ef63355f12a71ee1',
        ], $api->parseResponse([
            'paymentResult' => $paymentResult,
        ]));
    }

    public function testGenerateTestingData()
    {
        $api = new Api(
            $options = [
                'Version' => 'v1.0.0',
                'MerCode' => 'c5374addd1a3d024c3a026199cb8feaf',
                'MerKey' => 'c71530d016b3475579da8af971f7ca6c',
                'MerName' => null,
                'Account' => 'b02e072eee68d65bff916e08b4f11df2',
                'sandbox' => true,
            ],
            $httpClient = m::mock('Payum\Core\HttpClientInterface'),
            $message = m::mock('Http\Message\MessageFactory')
        );

        $this->assertTrue($api->isSandbox());
        $params = $api->parseResponse($api->generateTestingResponse());
        $this->assertSame('000000', $params['RspCode']);
    }
}
