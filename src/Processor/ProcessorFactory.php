<?php declare(strict_types=1);
/**
 *                       ######
 *                       ######
 * ############    ####( ######  #####. ######  ############   ############
 * #############  #####( ######  #####. ######  #############  #############
 *        ######  #####( ######  #####. ######  #####  ######  #####  ######
 * ###### ######  #####( ######  #####. ######  #####  #####   #####  ######
 * ###### ######  #####( ######  #####. ######  #####          #####  ######
 * #############  #############  #############  #############  #####  ######
 *  ############   ############  #############   ############  #####  ######
 *                                      ######
 *                               #############
 *                               ############
 *
 * Adyen Webhook Module for PHP
 *
 * Copyright (c) 2021 Adyen N.V.
 * This file is open source and available under the MIT license.
 * See the LICENSE file for more info.
 *
 */

namespace Adyen\Webhook\Processor;

use Adyen\Webhook\EventCodes;
use Adyen\Webhook\Exception\InvalidDataException;
use Adyen\Webhook\Notification;
use Psr\Log\LoggerInterface;

class ProcessorFactory
{
    private static $adyenEventCodeProcessors = [
        EventCodes::AUTHORISATION => AuthorisationProcessor::class,
        EventCodes::OFFER_CLOSED => OfferClosedProcessor::class,
        EventCodes::REFUND => RefundProcessor::class,
        EventCodes::REFUND_FAILED => RefundFailedProcessor::class
    ];

    /**
     * @throws InvalidDataException
     */
    public static function create(
        Notification $notification,
        string $paymentState,
        LoggerInterface $logger = null
    ): ProcessorInterface {
        /** @var Processor $processor */
        $processor = array_key_exists($notification->getEventCode(), self::$adyenEventCodeProcessors)
            ? new self::$adyenEventCodeProcessors[$notification->getEventCode()]($notification, $paymentState)
            : new DefaultProcessor($notification, $paymentState);

        if ($logger) {
            $processor->setLogger($logger);
        }

        return $processor;
    }
}
