<?php
namespace Commonhelp\Util\Security;


class StringUtils {
	/**
	 * Compares whether two strings are equal. To prevent guessing of the string
	 * length this is done by comparing two hashes against each other and afterwards
	 * a comparison of the real string to prevent against the unlikely chance of
	 * collisions.
	 *
	 * Be aware that this function may leak whether the string to compare have a different
	 * length.
	 *
	 * @param string $expected The expected value
	 * @param string $input The input to compare against
	 * @return bool True if the two strings are equal, otherwise false.
	 */
	public static function equals($expected, $input) {
		if(!is_string($expected) || !is_string($input)) {
			return false;
		}
		if(function_exists('hash_equals')) {
			return hash_equals($expected, $input);
		}
		$random = new SecureRandom();
		$randomString = $random->generate(10);
		if(hash('sha512', $expected.$randomString) === hash('sha512', $input.$randomString)) {
			if($expected === $input) {
				return true;
			}
		}
		return false;
	}
}