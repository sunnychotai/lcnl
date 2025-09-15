<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Verify your LCNL account</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, sans-serif; background-color:#f9f9f9; color:#333;">
  <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td align="center" style="padding:20px 0;">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" style="border:1px solid #e0e0e0; border-radius:8px; background:#ffffff; overflow:hidden;">
          <tr>
            <td style="background-color:#7a1d3c; padding:20px; text-align:center;">
              <h1 style="margin:0; font-size:20px; color:#ffffff; font-weight:bold;">Lohana Community North London</h1>
            </td>
          </tr>
          <tr>
            <td style="padding:30px;">
              <p style="font-size:16px; margin:0 0 20px 0;">Hello <?= esc($name) ?>,</p>

              <p style="font-size:16px; margin:0 0 20px 0;">
                Thank you for registering with the <strong>Lohana Community North London (LCNL)</strong>.
              </p>

              <p style="font-size:16px; margin:0 0 20px 0;">
                To activate your membership, please confirm your email by clicking the button below:
              </p>

              <p style="text-align:center; margin:30px 0;">
                <a href="<?= esc($link) ?>" style="background:#7a1d3c; color:#ffffff; text-decoration:none; padding:12px 24px; border-radius:30px; font-size:16px; display:inline-block;">
                  Verify My Account
                </a>
              </p>

              <p style="font-size:14px; color:#555;">
                Or copy and paste this link into your browser:<br>
                <a href="<?= esc($link) ?>" style="color:#7a1d3c; word-break:break-all;"><?= esc($link) ?></a>
              </p>

              <p style="font-size:14px; color:#777; margin-top:30px;">
                This link will expire in 48 hours. If you did not create an account, you can safely ignore this email.
              </p>
            </td>
          </tr>
          <tr>
            <td style="background-color:#f0f0f0; padding:15px; text-align:center; font-size:12px; color:#666;">
              &copy; <?= date('Y') ?> Lohana Community North London. All rights reserved.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
