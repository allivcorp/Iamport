<?php
namespace Alliv\Iamport\Test;

use Alliv\Iamport\Iamport;
use PHPUnit\Framework\TestCase;

class IamportTest extends TestCase
{
    /** @var Iamport */
    private $iamport;

    protected function setUp()
    {
        $this->iamport = new Iamport([
            'apiKey' => getenv('IAMPORT_REST_API_KEY'),
            'apiSecret' => getenv('IAMPORT_REST_API_SECRET')
        ]);
    }
    
    public function testGetPayments()
    {
        $response = $this->iamport->getPayments(Iamport::PAYMENT_STATUS_ALL, 1, 5);

        static::assertObjectHasAttribute('total', $response->data);
        static::assertObjectHasAttribute('previous', $response->data);
        static::assertObjectHasAttribute('next', $response->data);
        static::assertObjectHasAttribute('list', $response->data);
    }
}
