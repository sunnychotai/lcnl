<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>LCNL Contact Us Enquiry</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, sans-serif; background-color:#f9f9f9; color:#333;">
  <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td align="center" style="padding:20px 0;">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" style="border:1px solid #e0e0e0; border-radius:8px; background:#ffffff; overflow:hidden;">
          <tr>
            <td style="background-color:#7a1d3c; padding:20px; text-align:center;">
              <h1 style="margin:0; font-size:20px; color:#ffffff; font-weight:bold;">LCNL - Contact Us Form Submission</h1>
            </td>
          </tr>
          <tr>
            <td style="padding:30px;">
              <p style="font-size:16px; margin:0 0 20px 0;"><em>This message was sent to LCNL from the LCNL website (Contact Us) page</em></p>

              <p style="font-size:16px; margin:0 0 20px 0;">
                <p><strong>Name:</strong> <?= esc($name) ?></p>
<p><strong>Email:</strong> <?= esc($email) ?></p>
<p><strong>Subject:</strong> <?= esc($subject) ?></p>
<hr>
<p><?= $message ?></p>
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
