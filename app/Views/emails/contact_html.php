<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>New Contact Form Message</title>
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
              <p style="font-size:16px; margin:0 0 20px 0;">You have received a new contact form message:</p>

              <table cellpadding="6" cellspacing="0" border="0" width="100%" style="border-collapse:collapse; font-size:15px;">
                <tr>
                  <td style="width:120px; font-weight:bold; color:#555;">Name:</td>
                  <td><?= esc($name) ?></td>
                </tr>
                <tr>
                  <td style="width:120px; font-weight:bold; color:#555;">Email:</td>
                  <td><a href="mailto:<?= esc($email) ?>" style="color:#7a1d3c;"><?= esc($email) ?></a></td>
                </tr>
                <tr>
                  <td style="width:120px; font-weight:bold; color:#555;">Subject:</td>
                  <td><?= esc($subject) ?></td>
                </tr>
              </table>

              <hr style="margin:20px 0; border:0; border-top:1px solid #eee;">

              <p style="font-size:15px; line-height:1.6; white-space:pre-line;">
                <?= nl2br(esc($message)) ?>
              </p>
            </td>
          </tr>

          <!-- Footer -->
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
