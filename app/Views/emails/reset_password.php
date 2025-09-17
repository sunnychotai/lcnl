<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Password Reset - LCNL</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 15px; color: #333; background-color: #f9f9f9; padding: 20px; }
    .container { background: #fff; border-radius: 8px; padding: 20px; max-width: 600px; margin: auto; }
    .btn {
      display: inline-block;
      background: #7a1d3c; /* your brand colour */
      color: #fff !important;
      text-decoration: none;
      padding: 10px 18px;
      border-radius: 30px;
      font-weight: bold;
    }
    .small { font-size: 13px; color: #666; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Password Reset Request</h2>
    <p>Hi <?= esc($name) ?>,</p>

    <p>We received a request to reset your LCNL account password. If you made this request, click the button below:</p>

    <p style="text-align:center; margin: 20px 0;">
      <a href="<?= esc($link) ?>" class="btn">Reset Password</a>
    </p>

    <p>If the button above doesn’t work, copy and paste this link into your browser:</p>
    <p class="small"><?= esc($link) ?></p>

    <p>This link will expire in <strong>2 hours</strong>. If you didn’t request a password reset, you can safely ignore this email.</p>

    <p>Thanks,<br>The LCNL Team</p>
  </div>
</body>
</html>
