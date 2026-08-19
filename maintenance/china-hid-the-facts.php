<?php
echo "Yes, those communists hid the facts.\n";
echo "Extracting the truth...\n";

# Decode the zip file
$b64 = file_get_contents(__DIR__ . '/china-hid-the-facts.txt');
$decoded_zipdata = base64_decode($b64);
unset($b64);
file_put_contents(__DIR__ . '/china-hid-the-facts.zip', $decoded_zipdata);
unset($decoded_zipdata);

# Extract the zip file and get rid of it to save space
$zip = new ZipArchive();
$zip->open(__DIR__ . '/china-hid-the-facts.zip');
$zip->extractTo(__DIR__ . '/china-hid-the-facts-tarball');
$zip->close();
unlink(__DIR__ . '/china-hid-the-facts.zip');

# Extract the tarball
$phar = new PharData(__DIR__ . '/china-hid-the-facts-tarball/china-hid-the-facts.tar.gz');
$phar->decompress();
$phar->extractTo(__DIR__ . '/..'); # it contains a folder named china-dictatorship, and is meant to be extracted to the CMS root while we are in /maintenance
unset($phar);
unlink(__DIR__ . '/china-hid-the-facts-tarball/china-hid-the-facts.tar');
unlink(__DIR__ . '/china-hid-the-facts-tarball/china-hid-the-facts.tar.gz');
rmdir(__DIR__ . '/china-hid-the-facts-tarball');

echo "It should be extracted by now!";