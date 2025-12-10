<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Activate Your LCNL Account</title>
</head>

<body style="margin:0; padding:0; font-family:Arial, sans-serif; background-color:#f9f9f9; color:#333;">

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding:20px 0;">

                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600"
                    style="border:1px solid #e0e0e0; border-radius:8px; background:#ffffff;">

                    <!-- Header -->
                    <tr>
                        <td style="background-color:#7a1d3c; padding:20px; text-align:center;">
                            <h1 style="margin:0; font-size:20px; color:#ffffff; font-weight:bold;">
                                Lohana Community North London
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px;">

                            <h2 style="margin:0 0 20px 0; font-size:18px; color:#333;">Activate Your LCNL Online Account
                            </h2>

                            <p style="font-size:15px; margin:0 0 15px 0;">
                                Dear <?= esc($name ?: 'Member') ?>,
                            </p>

                            <p style="font-size:15px; margin:0 0 15px 0;">
                                You are registered with the <strong>Lohana Community North London (LCNL)</strong> as a
                                member.
                                We have now moved to a new <strong>online self-service membership portal</strong>.
                            </p>

                            <p style="font-size:15px; margin:0 0 15px 0;">
                                To get started, please activate your account by clicking the button below. You will be
                                able to set your password and securely log in for the first time.
                            </p>

                            <p style="text-align:center; margin:30px 0;">
                                <a href="<?= esc($link) ?>" style="background:#7a1d3c; color:#ffffff; text-decoration:none;
                                    padding:12px 24px; border-radius:30px; font-size:16px; display:inline-block;">
                                    Activate My Account
                                </a>
                            </p>

                            <p style="font-size:14px; color:#555;">
                                Or copy and paste this link into your browser:<br>
                                <a href="<?= esc($link) ?>" style="color:#7a1d3c; word-break:break-all;">
                                    <?= esc($link) ?>
                                </a>
                            </p>

                            <p style="font-size:15px; margin:25px 0 10px;">
                                Once activated, you will be able to:
                            </p>

                            <ul style="font-size:15px; color:#444; margin:0 0 25px 20px;">
                                <li>Set your account password</li>
                                <li>Review and update your personal details</li>
                                <li>Update family members linked to your account</li>
                                <li>Recertify your membership (confirm your details annually)</li>
                                <li>Access your member dashboard and upcoming LCNL updates</li>
                            </ul>

                            <p style="font-size:14px; color:#777;">
                                <strong>Security notice:</strong>
                                This activation link is valid for <strong>24 hours</strong>.
                                If it expires, please use the “Forgot Password” option on the login page to request a
                                new link.
                            </p>

                            <p style="font-size:14px; color:#777;">
                                <strong>If you did not expect this email:</strong><br>
                                It is safe to ignore it. Your account will remain inactive until you choose to activate
                                it.
                            </p>

                            <p style="font-size:15px; margin-top:30px;">
                                If you need any help, contact us at:<br>
                                <a href="mailto:members@lcnl.org" style="color:#7a1d3c;">membership@lcnl.org</a>
                            </p>

                            <p style="font-size:15px; margin-top:25px;">
                                Thank you,<br>
                                <strong>LCNL Membership Team</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f0f0f0; padding:15px; text-align:center;
                            font-size:12px; color:#666;">
                            &copy; <?= date('Y') ?> Lohana Community North London. All rights reserved.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
