# Paypal_Test

Welcome in your paypal_test site.

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/218c8f9ac8974de29727b5c226c2ffd2)](https://www.codacy.com/gh/thomasop/paypal_test/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=thomasop/paypal_test&amp;utm_campaign=Badge_Grade)

[![Maintainability](https://api.codeclimate.com/v1/badges/2b7cbab62eec111c8787/maintainability)](https://codeclimate.com/github/thomasop/paypal_test/maintainability)

[![Build Status](https://app.travis-ci.com/thomasop/paypal_test.svg?branch=master)](https://app.travis-ci.com/thomasop/paypal_test)

## How to install the project

### Prerequisite
PHP 8

Download Wamp, Xampp, Mamp or WebHost

Symfony 5.4

Composer

### Clone
Go in directory.
Make a clone with git clone https://github.com/thomasop/paypal_test.git

### Configuration
Update environnements variables in the .env file with your values.
At the very least you need to define the SYMFONY_ENV=prod
MAILER_URL

### Composer
Install composer with composer install and init the projet with composer init in paypal_test

### Start the project
At the root of your project use the command php bin/console server:start -d

### Test
For run test: make tests

### Google account
Create google account for to put client-id key in base.html.twig <script src="https://www.paypal.com/sdk/js?client-id=YOUR_KEY"></script>
