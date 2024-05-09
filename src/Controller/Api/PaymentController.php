<?php

namespace StinWeatherApp\Controller\Api;

use DateTime;
use Exception;
use StinWeatherApp\Component\Dto\PremiumPaymentRequestDto;
use StinWeatherApp\Component\Http\Request;
use StinWeatherApp\Component\Http\Response;
use StinWeatherApp\Component\Router\Router;
use StinWeatherApp\Controller\AbstractController;
use StinWeatherApp\Model\Builder\PaymentBuilder;
use StinWeatherApp\Model\Types\PaymentType;
use StinWeatherApp\Services\Payment\PaymentProcessingHandler;
use StinWeatherApp\Services\Payment\PaymentServiceProcess;
use StinWeatherApp\Services\Payment\Service\CardPaymentService;
use StinWeatherApp\Services\Payment\Service\CashPaymentService;
use StinWeatherApp\Services\PremiumPaymentRequest\PremiumPaymentProcessingHandler;
use StinWeatherApp\Services\PremiumPaymentRequest\PremiumPaymentTransformer;
use StinWeatherApp\Services\PremiumPaymentRequest\Validation\CurrencyValidationHandler;
use StinWeatherApp\Services\PremiumPaymentRequest\Validation\ExistsUserValidationHandler;
use StinWeatherApp\Services\PremiumPaymentRequest\Validation\PaymentTypeValidationHandler;
use StinWeatherApp\Services\PremiumPaymentRequest\Validation\PremiumValidationHandler;
use StinWeatherApp\Services\PremiumPaymentRequest\Validation\UserHasNotPremiumValidationHandler;
use StinWeatherApp\Services\PremiumPaymentRequest\Validation\ValidationHandler;

class PaymentController extends AbstractController {
	private PaymentProcessingHandler $paymentProcessingHandler;
	private PremiumPaymentProcessingHandler $premiumPaymentProcessingHandler;
	private ValidationHandler $validationHandler;

	public function __construct(Request $request) {
		parent::__construct($request);
		$premiumTransformer = new PremiumPaymentTransformer();
		$paymentProcess = new PaymentServiceProcess();
		$cardPaymentService = new CardPaymentService($paymentProcess);
		$cashPaymentService = new CashPaymentService($paymentProcess);

		$this->paymentProcessingHandler = new PaymentProcessingHandler(
			$cardPaymentService,
			$cashPaymentService
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

			// Create Data Transfer Object for Premium Payment Request
			$dto = new PremiumPaymentRequestDto();
			// Run validation chain. ORDER IS IMPORTANT
			$this->validationHandler = new ExistsUserValidationHandler($array, $dto);
			$this->validationHandler
				->setNext(new UserHasNotPremiumValidationHandler($array, $dto))
				->setNext(new PremiumValidationHandler($array, $dto))
				->setNext(new PaymentTypeValidationHandler($array, $dto))
				->setNext(new CurrencyValidationHandler($array, $dto));
			// Validate
			$this->validationHandler->handle();

			// Create instances from validated data
			$card = $dto->getCard();
			$premium = $dto->getPremium();
			$currency = $dto->getCurrency();
			$paymentType = $dto->getPaymentType();
			$user = $dto->getUser();


		} catch (Exception $e) {
			// Premium processing failed with exception
			return new Response(
				json_encode([
					"status" => "Request processing failed. {$e->getMessage()}"
				]) ?: "Premium processing failed. Please contact administrator.",
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

	public function options(): Response {
		$response = new Response("", 200);
		/** @var Router $GLOBALS ['router']; */
		$methods = $GLOBALS['router']->getAllowedMethods($this->request->getPath());
		$methods = array_map(fn($method) => $method->value, $methods);
		$response->setHeader("Access-Control-Allow-Methods: " . implode(", ", $methods));
		return $response;
	}

}