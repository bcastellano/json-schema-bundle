<?php

namespace Bcastellano\JsonSchemaBundle\Tests\Validator;

use Bcastellano\JsonSchemaBundle\Validator\JsonSchemaValidatorInterface;
use Bcastellano\JsonSchemaBundle\Validator\JsonSchemaValidatorSubscriber;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class JsonSchemaValidatorSubscriberTest extends \PHPUnit_Framework_TestCase
{
    public function testSubscribedEvents()
    {
        $events = JsonSchemaValidatorSubscriber::getSubscribedEvents();

        $this->assertEquals(
            [
                'kernel.request' => 'validateRequestBody',
                'kernel.response' => 'validateResponseBody',
            ],
            $events
        );
    }

    public function testValidateRequest()
    {
        $validator = $this->getMockBuilder(JsonSchemaValidatorInterface::class)->getMock();
        $request = $this->getMockBuilder(Request::class)->getMock();
        $event = $this->getMockBuilder(KernelEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $validator->expects($this->once())
            ->method('validateRequestBody')
            ->with($request);

        $subscriber = new JsonSchemaValidatorSubscriber($validator);
        $subscriber->validateRequestBody($event);
    }

    public function testValidateResponse()
    {
        $validator = $this->getMockBuilder(JsonSchemaValidatorInterface::class)->getMock();
        $request = $this->getMockBuilder(Request::class)->getMock();
        $response = $this->getMockBuilder(Response::class)->getMock();
        $event = $this->getMockBuilder(FilterResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);
        $event->expects($this->once())
            ->method('getResponse')
            ->willReturn($response);

        $validator->expects($this->once())
            ->method('validateResponseBody')
            ->with($request, $response);

        $subscriber = new JsonSchemaValidatorSubscriber($validator);
        $subscriber->validateResponseBody($event);
    }
}