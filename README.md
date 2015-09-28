# spanish-pronunciation-rules-php
A PHP function that converts a Spanish word (UTF-8 encoded) into IPA phonetic transcription symbols.

This function is used in the [Spanish phonetic transcription converter](http://easypronunciation.com/en/spanish-phonetic-transcription-converter). The converter can also insert stress marks in words and takes in consideration sound changes at word boundaries (sandhi). This function can only convert individual words.

List of supported locales:
* es_ES (Spain),
* es_MX (Mexico).

# Sample Usage
```PHP
$ipa = convert_spanish_word_to_phonetic_transcription ("amigo", "es_ES"); // returns "amiɣo"
$ipa = convert_spanish_word_to_phonetic_transcription ("pronunciación", "es_ES"); // returns "pɾonunθjaθjon"
$ipa = convert_spanish_word_to_phonetic_transcription ("pronunciación", "es_MX"); // returns "pɾonunsjasjon"
```

# Author
[Timur Baytukalov](http://easypronunciation.com/en/contacts)

Please feel free to contact me, if you want to report an error or suggest improvements.

# License
[GPL]

[GPL]: http://www.gnu.org/licenses/gpl.html