<?php
/**
 * spanish-pronunciation-rules-php -
 * a PHP function that converts a Spanish word (UTF-8 encoded) into IPA phonetic transcription symbols.
 * Supported locales: es_ES (Spain), es_MX (Mexico)
 * Written by Timur Baytukalov, http://easypronunciation.com/en/
 * Contact me at: http://easypronunciation.com/en/contacts
 * License: http://www.gnu.org/licenses/gpl.html
 * @version 0.1
 * Sample usage:
 * $ipa = convert_spanish_word_to_phonetic_transcription ("amigo", "es_ES"); // returns "amiɣo"
 */

// all the letters from the following array are pronounced always the same way by all Spanish speakers:
// (capital letter means that the vowel letter is stressed).
$array_spanish_letters = array ("a" => "a", "á" => "A", "e" => "e", "é" => "E", "f" => "f", "h" => "", "í" => "I", "j" => "x", "k" => "k", "m" => "m", "ñ" => "ɲ", "o" => "o", "ó" => "O", "p" => "p", "q" => "k", "t" => "t", "ú" => "U", "w" => "w");

// localized pronunciation variants:
$array_spanish_letters_localized = array ("z" => array("es_ES" => "θ", "es_MX" => "s"));
$array_spanish_letters_localized_variables = array ("c_before_i_or_e" => array("es_ES" => "θ", "es_MX" => "s"));

// supported locales:
$array_supported_spanish_locales = array ("es_ES", "es_MX");

// regular expression pattern to check if we have a word:
$regex_pattern_is_word = "/[A-Za-zÀ-ÿ]/u";

$array_s_becomes_z = array ("l", "m", "n", "b", "d", "g");
$array_v_becomes_b_transcription_signs = array ("m", "n", "ɲ");
$array_a_e_i_o_u = array ("a", "e", "i", "o", "u", "á", "é", "í", "ó", "ú");
$array_i_e_for_c_and_g = array ("e", "i", "é", "í");
$array_a_e_o_u_for_i = array ("a", "e", "o", "u", "á", "é", "ó", "ú");
$array_a_e_o_i_for_u = array ("a", "e", "o", "i", "á", "é", "ó", "í");
$array_a_o_u_for_nc = array ("a", "o", "u", "á", "ó", "ú");
$array_n_becomes_m = array ("b", "f", "m", "p", "v");

function convert_spanish_word_to_phonetic_transcription ($word, $locale) {

	global $array_spanish_letters,
		$array_spanish_letters_localized,
		$array_spanish_letters_localized_variables,
		$array_supported_spanish_locales,
		$regex_pattern_is_word,
		$array_s_becomes_z,
		$array_v_becomes_b_transcription_signs,
		$array_a_e_i_o_u,
		$array_i_e_for_c_and_g,
		$array_a_e_o_u_for_i,
		$array_a_e_o_i_for_u,
		$array_a_o_u_for_nc,
		$array_n_becomes_m;

	// we return an error, if the locale is not supported:
	if (!in_array($locale, $array_supported_spanish_locales)) {
		return false;
	}

	// we return an error, if the word doesn't contain any latin characters:
	if (!preg_match($regex_pattern_is_word, $word)) {
		return false;
	}

	// we create localized variables for Spanish dialects:
	foreach ($array_spanish_letters_localized_variables as $variable_name => $data_array) {
		$$variable_name = $data_array[$locale];
	}

	// we convert the word to lowercase:
	$word = mb_strtolower($word, "UTF-8");

	// length of the word:
	$length_of_word = mb_strlen($word, "UTF-8");

	// we set the future phonetic transcription
	$phonetic_transcription = "";

	// we set the variable that will allow us to skip some letters, if we want to:
	$skip_next_letter = 0;
	
	for ($current_position=1; $current_position<$length_of_word+1; $current_position++) {

		// we skip the current letter (repeat the cycle the desired number of times):
		if ($skip_next_letter > 0) {
			$skip_next_letter--;
			continue;
		}

		// we set the previous and the following letters:
		$current_letter = mb_substr($word, $current_position-1, 1, "UTF-8");
		$previous_letter = "";
		$next_letter = "";
		$after_next_letter = "";
		if ($current_position>1) { $previous_letter = mb_substr($word, $current_position-2, 1, "UTF-8"); }
		if ($current_position<$length_of_word) { $next_letter = mb_substr($word, $current_position, 1, "UTF-8"); }
		if ($current_position<$length_of_word-1) { $after_next_letter = mb_substr($word, $current_position+1, 1, "UTF-8"); }

		// we set the last transcription sign
		$last_transcription_sign = mb_substr($phonetic_transcription, mb_strlen($phonetic_transcription, "UTF-8")-1, 1, "UTF-8");

		// the letter is pronounced the same way by all Spanish speakers:
		if (array_key_exists($current_letter, $array_spanish_letters)) {
			$phonetic_transcription .= $array_spanish_letters[$current_letter];
			continue;
		}

		// the letter can be pronounced differently by Spanish speakers from different countries:
		if (array_key_exists($current_letter, $array_spanish_letters_localized)) {
			$phonetic_transcription .= $array_spanish_letters_localized[$current_letter][$locale];
			continue;
		}

		// letters "b" and "v" are equivalent:
		if (($current_letter == "b") || ($current_letter == "v")) {
			// at the beginning of a word
			if (($current_position == 1) ||
				// [mb], [nb], [ɲb]
				(in_array($last_transcription_sign, $array_v_becomes_b_transcription_signs))) {
				$phonetic_transcription .= "b";
			} else {
				$phonetic_transcription .= "β";
			}
			continue;
		}
	
		if ($current_letter == "c") {
			if (in_array($next_letter, $array_i_e_for_c_and_g)) {
				$phonetic_transcription .= $c_before_i_or_e;
				continue;
			}
			if ($next_letter == "h") {
				$phonetic_transcription .= "ʧ";
				$skip_next_letter = 1;
				continue;
			}
			$phonetic_transcription .= "k";
			continue;
		}

		if ($current_letter == "d") {
			// at the beginning of a word
			if (($current_position == 1) ||
				// [nd]
				($last_transcription_sign == "n") ||
				// [ld]
				($last_transcription_sign == "l")) {
				$phonetic_transcription .= "d";
			} else {
				$phonetic_transcription .= "ð";
			}
			continue;
		}
		
		if ($current_letter == "g") {
			if (in_array($next_letter, $array_i_e_for_c_and_g)) {
				$phonetic_transcription .= "x";
				continue;
			}
			// at the beginning of a word
			if (($current_position == 1) ||
				// "ng"
				($previous_letter == "n") ||
				// "lg"
				($previous_letter == "l")) {
				$phonetic_transcription .= "g";
				continue;
			}
			$phonetic_transcription .= "ɣ";
			continue;
		}

		if ($current_letter == "i") {
			if (in_array($next_letter, $array_a_e_o_u_for_i)) {
				$phonetic_transcription .= "j";
			} else {
				$phonetic_transcription .= "i";
			}
			continue;
		}

		if ($current_letter == "l") {
			// "ll"
			if ($next_letter == "l") {
				$phonetic_transcription .= "ʎ";
				$skip_next_letter = 1;
				continue;
			}
			$phonetic_transcription .= "l";
			continue;
		}

		if ($current_letter == "n") {
			if (in_array($next_letter, $array_n_becomes_m)) {
				$phonetic_transcription .= "m";
				continue;
			}
			// "nca", "nco", "ncu"
			if ((($next_letter == "c") && (in_array($after_next_letter, $array_a_o_u_for_nc))) ||
				// "nqu"
				(($next_letter == "q") && (($after_next_letter == "u") || ($after_next_letter == "ú"))) ||
				// "nk"
				($next_letter == "k") ||
				// "ng"
				($next_letter == "g") ||
				// "nj"
				($next_letter == "j")) {
				$phonetic_transcription .= "ŋ";
				continue;
			}
			// "nll"
			if ((($next_letter == "l") && ($after_next_letter == "l")) ||
				// "nch"
				(($next_letter == "c") && ($after_next_letter == "h")) ||
				// "nhi"
				(($next_letter == "h") && (($after_next_letter == "i") || ($after_next_letter == "í"))) ||
				// "ny"
				($next_letter == "y")) {
				$phonetic_transcription .= "ɲ";
				continue;
			}
			$phonetic_transcription .= "n";
			continue;
		}

		if ($current_letter == "r") {
			// at the beginning of a word
			if (($current_position == 1) ||
				// "nr"
				($last_transcription_sign == "n") ||
				// "lr"
				($last_transcription_sign == "l") ||
				// "sr"
				($last_transcription_sign == "s") ||
				// "rr"
				($next_letter == "r")) {
				$phonetic_transcription .= "r";
				if ($next_letter == "r") {
					$skip_next_letter = 1;
				}
				continue;
			}
			$phonetic_transcription .= "ɾ";
			continue;
		}
	
		if ($current_letter == "s") {
			if (in_array($next_letter, $array_s_becomes_z)) {
				$phonetic_transcription .= "z";
				continue;
			}
			$phonetic_transcription .= "s";
			continue;
		}

		if ($current_letter == "u") {
			// "gui", "gue" - not pronounced
			if ((($previous_letter == "g") && (in_array($next_letter, $array_i_e_for_c_and_g))) ||
				// "qu" - not pronounced
				($previous_letter == "q")) {
				continue;
			}
			// "ua", "ue", "ui", "uo"
			if (in_array($next_letter, $array_a_e_o_i_for_u)) {
				$phonetic_transcription .= "w";
				continue;
			}
			$phonetic_transcription .= "u";
			continue;
		}

		if ($current_letter == "ü") {
			// "üa", "üe", "üo", "üi"
			if (in_array($next_letter, $array_a_e_o_i_for_u)) {
				$phonetic_transcription .= "w";
				continue;
			}
			$phonetic_transcription .= "u";
			continue;
		}

		if ($current_letter == "x") {
			// words starting with "méxic", "mexic" are exceptions:
			if (($current_position == 3) && ((mb_substr($word, 0, 5, "UTF-8") == "méxic") || (mb_substr($word, 0, 5, "UTF-8") == "mexic"))) {
				$phonetic_transcription .= "x";
				continue;
			}
			$phonetic_transcription .= "ks";
			continue;
		}

		if ($current_letter == "y") {
			// the next letter is vowel
			if (in_array($next_letter, $array_a_e_i_o_u)) {
				$phonetic_transcription .= "ʝ";
				continue;
			}
			// the following is for proper handling of the accent position later:
			if (($length_of_word > 1) && ($current_position == $length_of_word)) {
				$phonetic_transcription .= "Y";
			} else {
				$phonetic_transcription .= "i";
			}
			continue;
		}			

	}

	// the following is to normalize the phonetic transcription to the IPA standard
	// we don't use this when processing the Spanish text
	$array_normalize_transcription_to_ipa = array("A" => "a", "E" => "e", "I" => "i", "O" => "o", "U" => "u", "Y" => "i");
	foreach ($array_normalize_transcription_to_ipa as $original => $replacement) {
		$pattern = "/" . $original . "/u";
//		$phonetic_transcription = preg_replace($pattern, $replacement, $phonetic_transcription);
	}

	return $phonetic_transcription;

}

?>