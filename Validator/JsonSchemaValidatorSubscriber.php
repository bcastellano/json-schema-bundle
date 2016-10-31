<?php

namespace Bcastellano\JsonSchemaBundle\Validator;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class JsonSchemaValidatorSubscriber implements EventSubscriberInterface
{
    /**
     * @var JsonSchemaValidatorInterface
     */
    protected $validator;

    public function __construct(JsonSchemaValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validate json body request
     *
     * @param KernelEvent $event
     */
    public function validateRequestBody(KernelEvent $event)
    {
        $this->validator->validateRequestBody($event->getRequest());
    }

    /**
     * Validate json body response
     *
     * @param FilterResponseEvent $event
     */
    public function validateResponseBody(FilterResponseEvent $event)
    {
        $this->validator->validateResponseBody($event->getRequest(), $event->getResponse());
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'validateRequestBody',
            KernelEvents::RESPONSE => 'validateResponseBody'
        ];
    }
}