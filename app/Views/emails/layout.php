<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'LCNL Notification') ?></title>
</head>

<body style="margin:0; padding:0; font-family:Arial, sans-serif; background-color:#f9f9f9; color:#333;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:20px 0;">

                <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                    style="border:1px solid #e0e0e0; border-radius:8px; background:#ffffff;">

                    <!-- Header -->
                    <tr>
                        <td style="background-color:#7a1d3c; padding:20px; text-align:center;">
                            <img src="https://lcnl.org/assets/img/lcnl-logo.png"
                                alt="LCNL Logo"
                                width="60"
                                height="60"
                                style="display:block; margin:0 auto 8px;">
                            <h1 style="margin:0; font-size:20px; color:#ffffff; font-weight:bold;">
                                Lohana Community North London
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px; font-size:15px; line-height:1.6;">
                            <?= $this->renderSection('content') ?>
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