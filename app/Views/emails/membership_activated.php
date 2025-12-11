<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your LCNL Membership is Now Active</title>
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

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="text-align:center;">
                                <tr>
                                    <td align="center">

                                        <!-- Logo -->
                                        <img src="https://lcnl.org/assets/img/lcnl-logo.png" alt="LCNL Logo" width="60"
                                            height="60" style="display:block; margin:0 auto 8px auto;">

                                        <!-- Title -->
                                        <h1 style="margin:0; font-size:20px; color:#ffffff; font-weight:bold;">
                                            Lohana Community North London
                                        </h1>

                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px;">

                            <h2 style="margin:0 0 20px 0; font-size:18px; color:#333;">Your LCNL Account is Now Active
                            </h2>

                            <p style="font-size:15px; margin:0 0 15px 0;">
                                Dear <?= esc($name ?: 'Member') ?>,
                            </p>

                            <p style="font-size:15px; margin:0 0 15px 15px;">
                                Your membership has now been <strong>successfully activated</strong>,
                                and your online LCNL account is ready for use.
                            </p>

                            <p style="font-size:15px; margin:0 0 20px 0;">
                                You can now sign in at any time using your email and the password you have just set.
                            </p>

                            <p style="text-align:center; margin:30px 0;">
                                <a href="<?= base_url('membership/login') ?>" style="background:#7a1d3c; color:#ffffff; text-decoration:none;
                                   padding:12px 24px; border-radius:30px; font-size:16px; display:inline-block;">
                                    Go to Login
                                </a>
                            </p>

                            <p style="font-size:15px; margin-top:20px;">
                                Inside your dashboard you can:
                            </p>

                            <ul style="font-size:15px; color:#444; margin:0 0 25px 20px;">
                                <li>Review and update your personal information</li>
                                <li>Manage your family members</li>
                                <li>Recertify your membership annually</li>
                                <li>Receive community updates and information</li>
                            </ul>

                            <p style="font-size:14px; color:#777;">
                                If you did not expect this activation, please contact us immediately.
                            </p>

                            <p style="font-size:15px; margin-top:30px;">
                                Thank you,<br>
                                <strong>LCNL Membership Team</strong><br>
                                <a href="mailto:membership@lcnl.org" style="color:#7a1d3c;">membership@lcnl.org</a>
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
