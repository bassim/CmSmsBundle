<?php

namespace Bassim\CmSmsBundle\Tests\Services;

use Bassim\CmSmsBundle\Services\CmSmsService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CmSmsServiceTest extends WebTestCase
{
    public function testIndex()
    {
        //bassim_cm_sms.sms_service
        $service = new CmSmsService("0","username","password","http://localhost");


        $this->assertTrue($service->send("sender", "body", "number"));
    }
}
