<?php

namespace Services\Payment;

use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\SQLiteConnectionBuilder;
use StinWeatherApp\Model\Payment;
use StinWeatherApp\Model\Types\Currency;
use StinWeatherApp\Model\Types\PaymentType;
use StinWeatherApp\Services\Payment\PaymentProcessingHandler;
use StinWeatherApp\Services\Payment\PaymentServiceProcess;
use StinWeatherApp\Services\Payment\Service\CardPaymentService;
use StinWeatherApp\Services\Payment\Service\CashPaymentService;

class PaymentProcessingHandlerTest extends TestCase {
	public function testPaymentProcessingHandlerProcessesCardPaymentSuccessfully(): void {
		$paymentProcess = new PaymentServiceProcess();
		$payment = new Payment(10000, Currency::CZK, new DateTime(), PaymentType::CARD, 'PREPROCESSING');
		$paymentProcessingHandler = new PaymentProcessingHandler(new CardPaymentService($paymentProcess), new CashPaymentService($paymentProcess));

		$this->assertTrue($paymentProcessingHandler->processPayment($payment));
		$this->assertEquals('DONE', $payment->getStatus());
	}

	public function testPaymentProcessingHandlerProcessesCashPaymentSuccessfully(): void {
		$paymentProcess = new PaymentServiceProcess();
		$payment = new Payment(10000, Currency::CZK, new DateTime(), PaymentType::CASH, 'PREPROCESSING');
		$paymentProcessingHandler = new PaymentProcessingHandler(new CardPaymentService($paymentProcess), new CashPaymentService($paymentProcess));

		$this->assertTrue($paymentProcessingHandler->processPayment($payment));
		$this->assertEquals('DONE', $payment->getStatus());
	}

	public function testPaymentProcessingHandlerProcessesCashPaymentFailed(): void {
		$paymentProcess = new PaymentServiceProcess();
		$payment = new Payment(100, Currency::CZK, new DateTime(), PaymentType::CASH, 'PREPROCESSING');
		$paymentProcessingHandler = new PaymentProcessingHandler(new CardPaymentService($paymentProcess), new CashPaymentService($paymentProcess));

		$this->assertFalse($paymentProcessingHandler->processPayment($payment));
		$this->assertEquals('FAILED', $payment->getStatus());
	}

	/**
	 * @throws \PHPUnit\Framework\MockObject\Exception
	 * @throws Exception
	 */
	public function testPaymentProcessingHandlerFailsToProcessCardPayment(): void {
		// Mock CardPaymentService
		$cardPaymentService = $this->createMock(CardPaymentService::class);
		$cardPaymentService->method('processPayment')->willReturn(false);

		// Mock CashPaymentService
		$cashPaymentService = $this->createMock(CashPaymentService::class);
		$cashPaymentService->method('processPayment')->willReturn(false);

		$payment = new Payment(10000, Currency::CZK, new DateTime(), PaymentType::CASH, 'PREPROCESSING');
		$paymentProcessingHandler = new PaymentProcessingHandler($cardPaymentService, $cashPaymentService);

		$this->assertFalse($paymentProcessingHandler->processPayment($payment));
		$this->assertEquals('FAILED', $payment->getStatus());
	}

	protected function setUp(): void {
		parent::setUp();

		// Make Database in-memory connection
		$conn = new SQLiteConnectionBuilder();
		$conn->setDatabase(':memory:');
		$conn->buildConnection();
		Db::connect($conn);

		// Create the user table
		Db::execute("
            CREATE TABLE user (
                id INTEGER PRIMARY KEY,
                username TEXT,
                api_key TEXT,
                premium_until TEXT
            )
        ");
		// Create the payment table
		Db::execute("
			CREATE TABLE payment
			(
			    id       integer  not null
			        constraint payment_pk
			            primary key autoincrement,
			    amount   float    not null,
			    currency string,
			    datetime datetime not null,
			    type     string   not null,
			    status   string   not null
			);
		");
	}
}