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

		// Extract card, premium and user from payload


		// Get validated associative array of payload
		try {
			/** @var array<string, string|array<string, string>> $array */
			$array = $this->premiumPaymentProcessingHandler->getPremiumFromPayload($payload);
			$premium = Premium::getById($array["premiumOption"]);
			$card = $this->extractCard($array[PremiumPaymentParserInterface::cardKey]);
			$user = User::getUserByUsername($array["username"]);
			$paymentType = PaymentType::tryFrom(strtoupper($array["paymentType"]));
			$currency = Currency::fromString($array["currency"]);
			if ($premium === null) {
				throw new Exception("Invalid premium option");
			}
			if ($user === null) {
				throw new Exception("Invalid user");
			} elseif ($user->getPremiumUntil() !== null && $user->getPremiumUntil() > new DateTime("now")) {
				throw new Exception("User already has premium");
			}
			if ($paymentType === null) {
				throw new Exception("Invalid payment type");
			}
			if ($currency === null) {
				throw new Exception("Invalid currency");
			}
		} catch (Exception $e) {
			// Premium processing failed with exception
			return new Response(
				json_encode([
					"status" => "Request processing failed. {$e->getMessage()}"
				]) ?: "Premium processing failed",
				400);
		}
		$pb = (new PaymentBuilder())
			->setAmount($premium->getPrice())
			->setCurrency($currency)
			->setType($paymentType)
			->setDatetime(new DateTime())
			->setStatus("NONE");


		if ($paymentType === PaymentType::CARD && $card !== null) {
			$pb->setCard($card);
		}
		$payment = $pb->build();

		try {
			$success = $this->paymentProcessingHandler->processPayment($payment);
			// Payment processed successfully
			if ($success) {
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
	 */
	private function extractCard(array $array): ?Card {
		$cardKey = PremiumPaymentParserInterface::cardKey;
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