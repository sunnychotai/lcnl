<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title><?= esc($subject ?? 'LCNL Test Email') ?></title>
  <style>
    body { font-family: Arial, sans-serif; background: #f6f6f6; margin: 0; padding: 20px; }
    .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; }
    .header { background: #7a1d3c; color: #fff; padding: 20px; text-align: center; }
    .header img { max-height: 60px; margin-bottom: 10px; }
    .content { padding: 20px; color: #333; }
    .footer { background: #f0f0f0; color: #666; font-size: 12px; padding: 15px; text-align: center; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src="<?= base_url('assets/img/lcnl-placeholder-320.png') ?>" alt="LCNL Logo">
      <h1>LCNL - Message from LCNL Website</h1>
    </div>
    <div class="content">
      <p>Hello <?= esc($name ?? 'Friend') ?>,</p>
      <p><strong>Name:</strong> <?= esc($name) ?></p>
<p><strong>Email:</strong> <?= esc($email) ?></p>
<p><strong>Subject:</strong> <?= esc($subject) ?></p>
<hr>
<p><?= $message ?></p>
   
    </div>
    <div class="footer">
      &copy; <?= date('Y') ?> Lohana Community of North London. All rights reserved.
    </div>
  </div>
</body>
</html>



