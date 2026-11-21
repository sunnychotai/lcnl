<?php

/**
 * A tiny SMTP server for tests: php FakeSmtpServer.php <port>
 *
 * Accepts AUTH LOGIN with any credentials. A message whose body contains
 * "SPAMMY" is refused the way Hostinger refused real mail on 21 Nov 2026
 * ("554 5.7.1 Spam message rejected"); anything else is accepted.
 */

$port   = (int) ($argv[1] ?? 2525);
$server = stream_socket_server("tcp://127.0.0.1:{$port}", $errno, $errstr);

if (!$server) {
    fwrite(STDERR, "cannot listen: {$errstr}\n");
    exit(1);
}

while ($conn = @stream_socket_accept($server, 30)) {
    $say = static fn (string $line) => fwrite($conn, $line . "\r\n");
    $say('220 fake.smtp ESMTP ready.');

    $authStep = 0;
    while (($line = fgets($conn)) !== false) {
        $cmd = strtoupper(substr(trim($line), 0, 4));

        if ($authStep > 0) {           // username, then password
            $say($authStep++ === 1 ? '334 UGFzc3dvcmQ6' : '235 2.7.0 Authentication successful');
            if ($authStep > 2) {
                $authStep = 0;
            }
            continue;
        }

        switch ($cmd) {
            case 'EHLO':
            case 'HELO':
                $say('250-fake.smtp');
                $say('250 AUTH PLAIN LOGIN');
                break;

            case 'AUTH':
                $authStep = 1;
                $say('334 VXNlcm5hbWU6');
                break;

            case 'MAIL':
            case 'RCPT':
                $say('250 2.1.0 OK');
                break;

            case 'DATA':
                $say('354 OK');
                $body = '';
                while (($data = fgets($conn)) !== false && rtrim($data, "\r\n") !== '.') {
                    $body .= $data;
                }
                $say(str_contains($body, 'SPAMMY') ? '554 5.7.1 Spam message rejected' : '250 2.0.0 Ok: queued');
                break;

            case 'RSET':
            case 'NOOP':
                $say('250 OK');
                break;

            case 'QUIT':
                $say('221 2.0.0 Bye');
                fclose($conn);
                continue 3;

            default:
                $say('502 5.5.2 Command not recognised');
        }
    }

    @fclose($conn);
}
