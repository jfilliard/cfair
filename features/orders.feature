Feature: Orders
	As a user
	In order to exchange money
	I want to place order

	@api
	Scenario: Message consuming
		When I post a message to the consumer
			"""
			{
				"userId": "134256",
				"currencyFrom": "EUR",
				"currencyTo": "GBP",
				"amountSell": 1000,
				"amountBuy": 747.10,
				"rate": 0.7471,
				"timePlaced" : "24-JAN-15 10:27:44",
				"originatingCountry" : "FR"
			}
			"""
		Then a job should be queued

	@cli
	Scenario: Message processing
		Given there is a pending job
			"""
			{
				"userId": "134256",
				"currencyFrom": "EUR",
				"currencyTo": "GBP",
				"amountSell": 1000,
				"amountBuy": 747.10,
				"rate": 0.7471,
				"timePlaced" : "24-JAN-15 10:27:44",
				"originatingCountry" : "FR"
			}
			"""
		When the message processor pick it
		Then no job should still be pending
		And user 134256 should have one order placed at "24-JAN-15 10:27:44"
		And stats for currency "EUR" should be 1000 - 0
		And stats for currency "GBP" should be 0 - 747.10

	@cli
	Scenario: Process multiple messages
		Given there is a pending job
			"""
			{
				"userId": "134256",
				"currencyFrom": "EUR",
				"currencyTo": "GBP",
				"amountSell": 1000,
				"amountBuy": 747.10,
				"rate": 0.7471,
				"timePlaced" : "24-JAN-15 10:27:44",
				"originatingCountry" : "FR"
			}
			"""
		And there is a pending job
			"""
			{
				"userId": "134257",
				"currencyFrom": "EUR",
				"currencyTo": "GBP",
				"amountSell": 500,
				"amountBuy": 400,
				"rate": 0.8,
				"timePlaced" : "24-JAN-15 14:35:02",
				"originatingCountry" : "BE"
			}
			"""
		And there is a pending job
			"""
			{
				"userId": "134258",
				"currencyFrom": "GBP",
				"currencyTo": "EUR",
				"amountSell": 100,
				"amountBuy": 123.7,
				"rate": 1.237,
				"timePlaced" : "24-JAN-15 23:40:12",
				"originatingCountry" : "IT"
			}
			"""
		And there is a pending job
			"""
			{
				"userId": "134258",
				"currencyFrom": "GBP",
				"currencyTo": "NZD",
				"amountSell": 100,
				"amountBuy": 199.63,
				"rate": 1.9963,
				"timePlaced" : "24-JAN-15 23:44:57",
				"originatingCountry" : "IT"
			}
			"""
		When the message processor pick it
		Then no job should still be pending
		And user 134256 should have one order placed at "24-JAN-15 10:27:44"
		And user 134257 should have one order placed at "24-JAN-15 14:35:02"
		And user 134258 should have one order placed at "24-JAN-15 23:40:12"
		And user 134258 should have one order placed at "24-JAN-15 23:44:57"
		And stats for currency "EUR" should be 1500 - 123.7
		And stats for currency "GBP" should be 200 - 1147.10
		And stats for currency "NZD" should be 0 - 199.63
