<?php
namespace Commonhelp\Util\Security;

use phpseclib\Crypt\AES;
use phpseclib\Crypt\Hash;
use Commonhelp\App\SystemConfig;

class Crypto{
	
	/** @var AES $cipher */
	private $cipher;
	/** @var int */
	private $ivLength = 16;
	/** @var SystemConfig */
	private $config;
	/** @var SecureRandom */
	private $random;
	
	/**
	 * @param SecurityConfig $config
	 * @param SecureRandom $random
	 */
	function __construct(SystemConfig $config, SecureRandom $random) {
		$this->cipher = new AES();
		$this->config = $config;
		$this->random = $random;
	}
	
	/**
	 * @param string $message The message to authenticate
	 * @param string $password Password to use (defaults to `secret` in config.php)
	 * @return string Calculated HMAC
	 */
	public function calculateHMAC($message, $password = '') {
		if($password === '') {
			$password = $this->config->getSecret();
		}
		// Append an "a" behind the password and hash it to prevent reusing the same password as for encryption
		$password = hash('sha512', $password . 'a');
		$hash = new Hash('sha512');
		$hash->setKey($password);
		return $hash->hash($message);
	}
	/**
	 * Encrypts a value and adds an HMAC (Encrypt-Then-MAC)
	 * @param string $plaintext
	 * @param string $password Password to encrypt, if not specified the secret from config.php will be taken
	 * @return string Authenticated ciphertext
	 */
	public function encrypt($plaintext, $password = '') {
		if($password === '') {
			$password = $this->config->getSecret();
		}
		$this->cipher->setPassword($password);
		$iv = $this->random->getLowStrengthGenerator()->generate($this->ivLength);
		$this->cipher->setIV($iv);
		$ciphertext = bin2hex($this->cipher->encrypt($plaintext));
		$hmac = bin2hex($this->calculateHMAC($ciphertext.$iv, $password));
		return $ciphertext.'|'.$iv.'|'.$hmac;
	}
	/**
	 * Decrypts a value and verifies the HMAC (Encrypt-Then-Mac)
	 * @param string $authenticatedCiphertext
	 * @param string $password Password to encrypt, if not specified the secret from config.php will be taken
	 * @return string plaintext
	 * @throws \Exception If the HMAC does not match
	 */
	public function decrypt($authenticatedCiphertext, $password = '') {
		if($password === '') {
			$password = $this->config->getSecret();
		}
		$this->cipher->setPassword($password);
		$parts = explode('|', $authenticatedCiphertext);
		if(sizeof($parts) !== 3) {
			throw new \Exception('Authenticated ciphertext could not be decoded.');
		}
		$ciphertext = hex2bin($parts[0]);
		$iv = $parts[1];
		$hmac = hex2bin($parts[2]);
		$this->cipher->setIV($iv);
		if(!\OCP\Security\StringUtils::equals($this->calculateHMAC($parts[0].$parts[1], $password), $hmac)) {
			throw new \Exception('HMAC does not match.');
		}
		return $this->cipher->decrypt($ciphertext);
	}
	
}