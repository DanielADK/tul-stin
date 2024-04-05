<?php

namespace Model;

use DateTime;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\SQLiteConnectionBuilder;
use StinWeatherApp\Model\Payment;
use StinWeatherApp\Model\Types\Currency;
use StinWeatherApp\Model\Types\PaymentType;

class PaymentTest extends TestCase {

	private Payment $payment;

	protected function setUp(): void {
		parent::setUp();

		// Make Database in-memory connection
		$conn = new SQLiteConnectionBuilder();
		$conn->setDatabase(':memory:');
		$conn->buildConnection();
		Db::connect($conn);

		// Create the payment table
		Db::execute("
            CREATE TABLE payment (
                id INTEGER PRIMARY KEY,
                amount FLOAT,
                currency TEXT,
                datetime TEXT,
                type TEXT,
                status TEXT
            )
        ");

		$this->payment = new Payment(100.0, Currency::CZK, new DateTime(), PaymentType::CASH, 'pending');
	}

	public function testGetAndSetAmount(): void {
		$this->payment->setAmount(200.0);
		$this->assertSame(200.0, $this->payment->getAmount());
	}

	public function testGetAndSetCurrency(): void {
		$this->payment->setCurrency(Currency::EUR);
		$this->assertSame(Currency::EUR, $this->payment->getCurrency());
	}

	public function testGetAndSetDatetime(): void {
		$datetime = new DateTime('2022-01-01');
		$this->payment->setDatetime($datetime);
		$this->assertSame($datetime, $this->payment->getDatetime());
	}

	public function testGetAndSetType(): void {
		$this->payment->setType(PaymentType::CARD);
		$this->assertSame(PaymentType::CARD, $this->payment->getType());
	}

	public function testGetAndSetStatus(): void {
		$this->payment->setStatus('completed');
		$this->assertSame('completed', $this->payment->getStatus());
	}


	/**
	 * @throws \Exception
	 */
	public function testPersistAndGetById(): void {
		try {
			// Persist the payment
			$this->payment->persist();
		} catch (\Exception $e) {
			$this->fail('Failed to persist the payment: ' . $e->getMessage());
		}

		// Get the payment by id
		$persistedPayment = Payment::getById($this->payment->getId());

		// Assert the persisted payment is not null and its properties match the original payment
		$this->assertNotNull($persistedPayment);
		$this->assertEquals($this->payment->getAmount(), $persistedPayment->getAmount());
		$this->assertEquals($this->payment->getCurrency(), $persistedPayment->getCurrency());
		$this->assertEquals($this->payment->getDatetime(), $persistedPayment->getDatetime());
		$this->assertEquals($this->payment->getType(), $persistedPayment->getType());
		$this->assertEquals($this->payment->getStatus(), $persistedPayment->getStatus());
	}
}