<?php

namespace Tests\Unit;

use App\Libraries\ContactSpamCheck;
use App\Libraries\SmtpError;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class SmtpErrorAndSpamCheckTest extends CIUnitTestCase
{
    // CodeIgniter's printDebugger([]) output for failures seen on production (21 Nov 2026).
    private const SPAM = "Unable to send email using SMTP. Your server might not be configured to send mail using this method.<br>"
        . "554 5.7.1 Spam message rejected\r\n<br>"
        . "The following SMTP error was encountered: 554 5.7.1 Spam message rejected\r\n<br>";

    private const AUTH_START = "Unable to initiate AUTH command. Your server might not be configured to use AUTH LOGIN authentication method.<br>"
        . "Unable to send email using SMTP. Your server might not be configured to send mail using this method.<br>";

    private const AUTH_USER = "Failed to authenticate username. Error: 501 5.5.2 Invalid base64 data in continued response\r\n<br>"
        . "Unable to send email using SMTP. Your server might not be configured to send mail using this method.<br>";

    public function testSummariesKeepTheServersReason(): void
    {
        $this->assertSame('554 5.7.1 Spam message rejected', SmtpError::summarise(self::SPAM));
        $this->assertSame(
            'AUTH: Unable to initiate AUTH command. Your server might not be configured to use AUTH LOGIN authentication method.',
            SmtpError::summarise(self::AUTH_START)
        );
        $this->assertSame(
            'AUTH: Failed to authenticate username. Error: 501 5.5.2 Invalid base64 data in continued response',
            SmtpError::summarise(self::AUTH_USER)
        );
        $this->assertSame(
            'CONNECT: 111 Connection refused',
            SmtpError::summarise('The following SMTP error was encountered: 111 Connection refused<br>')
        );
        $this->assertFalse(SmtpError::isPermanent('CONNECT: 111 Connection refused'));
        $this->assertSame('SMTP send failed', SmtpError::summarise(''));
    }

    public function testOnlyRejectionsOfTheMessageArePermanent(): void
    {
        $this->assertTrue(SmtpError::isPermanent(SmtpError::summarise(self::SPAM)));
        $this->assertTrue(SmtpError::isPermanent('550 5.1.1 No such user'));

        // A 501 during login is a connection problem, not a verdict on the email.
        $this->assertFalse(SmtpError::isPermanent(SmtpError::summarise(self::AUTH_USER)));
        $this->assertFalse(SmtpError::isPermanent('421 4.7.0 Try again later'));
        $this->assertFalse(SmtpError::isPermanent('SMTP send failed'));
        $this->assertFalse(SmtpError::isPermanent(null));
    }

    public function testOrdinaryMessagesPassTheContentCheck(): void
    {
        $this->assertNull(ContactSpamCheck::problem([
            'name'    => 'Priya Shah',
            'subject' => 'Navratri volunteering',
            'message' => 'Hi, I saw https://lcnl.org/events and www.example.com/rota, can I help?',
        ]));
        $this->assertNull(ContactSpamCheck::problem([
            'name'    => 'પ્રિયા શાહ',
            'subject' => 'દિવાળી',
            'message' => 'નમસ્તે, દિવાળીના કાર્યક્રમ વિશે માહિતી જોઈએ છે.',
        ]));
    }

    public function testLinkStuffedMessagesAreTurnedBack(): void
    {
        $this->assertNotNull(ContactSpamCheck::problem([
            'message' => 'Cheap SEO https://a.example https://b.example https://c.example',
        ]));
        $this->assertNotNull(ContactSpamCheck::problem(['message' => 'Visit [url=https://x.example]here[/url] now']));
        $this->assertNotNull(ContactSpamCheck::problem(['message' => 'See <a href="https://x.example">this</a>']));
        $this->assertNotNull(ContactSpamCheck::problem(['name' => 'https://casino.example', 'message' => 'Hello there friend']));
    }
}
