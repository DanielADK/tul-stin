<?php

namespace StinWeatherApp\Controller\Api;

use DateTime;
use Exception;
use StinWeatherApp\Component\Http\Request;
use StinWeatherApp\Component\Http\Response;
use StinWeatherApp\Controller\AbstractController;
use StinWeatherApp\Model\Builder\PaymentBuilder;
use StinWeatherApp\Model\Buyable\Premium;
use StinWeatherApp\Model\Card;
use StinWeatherApp\Model\Types\Currency;
use StinWeatherApp\Model\Types\PaymentType;
use StinWeatherApp\Model\User;
use StinWeatherApp\Services\Payment\PaymentProcessingHandler;
use StinWeatherApp\Services\Payment\PaymentServiceProcess;
use StinWeatherApp\Services\Payment\Service\CardPaymentService;
use StinWeatherApp\Services\Payment\Service\CashPaymentService;
use StinWeatherApp\Services\PremiumPaymentRequest\Parser\PremiumPaymentParserInterface;
use StinWeatherApp\Services\PremiumPaymentRequest\PremiumPaymentProcessingHandler;
use StinWeatherApp\Services\PremiumPaymentRequest\PremiumPaymentTransformer;

class PaymentController extends AbstractController {
	private PaymentProcessingHandler $paymentProcessingHandler;
	private PremiumPaymentProcessingHandler $premiumPaymentProcessingHandler;

	public function __construct(Request $request) {
		parent::__construct($request);
		$premiumTransformer = new PremiumPaymentTransformer();
		$paymentProcess = new PaymentServiceProcess();
		$cardPaymentService = new CardPaymentService($paymentProcess);
		$cashPaymentService = new CashPaymentService($paymentProcess);

		$this->paymentProcessingHandler = new PaymentProcessingHandler(
			$cardPaymentService,
			$cashPaymentService,
			$premiumTransformer
		);
		$this->premiumPaymentProcessingHandler = new PremiumPaymentProcessingHandler(
			$premiumTransformer
		);
	}

	/**
	 * @description Processes the payment
	 *
	 * @return Response
	 * @throws Exception
	 */
	public function processPayment(): Response {
		// Get the raw body of the request
		$payload = $this->request->getRawBody();

		try {
			// Parse payload to associative array

			/** @var array<string, string|array<string, string>> $array */
			$array = $this->premiumPaymentProcessingHandler->getPremiumFromPayload($payload);
			error_log(json_encode($array));

			// Extract Premium from array and check if it is valid
			$premium = Premium::getById($array["premiumOption"]);
			if ($premium === null) {
				throw new Exception("Invalid premium option.");
			}

			// Extract payment type from array and check if it is valid
			$paymentType = PaymentType::tryFrom(strtoupper($array["paymentType"]));
			if ($paymentType === null) {
				throw new Exception("Invalid payment type.");
			}

			// Extract card from array and check if it is valid
			$card = $this->extractCard($array);
			if ($card === null && $paymentType === PaymentType::CARD) {
				throw new Exception("Invalid card information.");
			}

			// Extract user from array and check if it is valid and does not have premium
			$user = User::getUserByUsername($array["username"]);
			if ($user === null) {
				throw new Exception("Invalid user.");
			} elseif ($user->getPremiumUntil() !== null && $user->getPremiumUntil() > new DateTime("now")) {
				throw new Exception("User already has premium.");
			}

			// Extract currency from array
			$currency = Currency::fromString($array["currency"]);
			if ($currency === null) {
				throw new Exception("Invalid currency.");
			}
		} catch (Exception $e) {
			// Premium processing failed with exception
			return new Response(
				json_encode([
					"status" => "Request processing failed. {$e->getMessage()}"
				]) ?: "Premium processing failed",
				400);
		}

		// Build payment
		$pb = (new PaymentBuilder())
			->setAmount($premium->getPrice())
			->setCurrency($currency)
			->setType($paymentType)
			->setDatetime(new DateTime())
			->setStatus("NONE");

		// Set card if payment type is CARD
		if ($paymentType === PaymentType::CARD && $card !== null) {
			$pb->setCard($card);
		}
		$payment = $pb->build();

		try {
			// Process payment
			$success = $this->paymentProcessingHandler->processPayment($payment);

			if ($success) {
				// Payment processed successfully
				$premiumUntil = new DateTime("now");
				$premiumUntil->setTimestamp($premiumUntil->getTimestamp() + $premium->getDuration());
				$user->generateApiKey()
					->setPremiumUntil($premiumUntil)
					->persist();
				$response = new Response(
					json_encode([
						"status" => "Payment processed successfully. Your API KEY is {$user->getApiKey()}"
					]) ?: "Payment processed successfully",
					200);
			} else {
				// Payment processing failed
				$response = new Response(
					json_encode([
						"status" => "Payment processing failed"
					]) ?: "Payment processing failed",
					400);
			}

		} catch (Exception $e) {
			// Payment processing failed with exception
			return new Response(
				json_encode([
					"status" => "Payment processing failed. {$e->getMessage()}"
				]) ?: "Payment processing failed",
				400);
		}
		return $response;
	}

	/**
	 * @description Extracts card from array
	 *
	 * @param array<string, array<string, string>> $array
	 *
	 * @return ?Card
	 * @throws Exception
	 */
	private function extractCard(array $array): ?Card {
		$cardKey = PremiumPaymentParserInterface::cardKey;
		// Check if card key is in array
		if (!array_key_exists($cardKey, $array)) {
			return null;
		}
		$cardArray = $array[$cardKey];

		return new Card(
			$cardArray["cardNumber"],
			$cardArray["cardExpiration"],
			$cardArray["cardCode"]
		);
	}

}