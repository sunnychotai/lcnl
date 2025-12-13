<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>LCNL Chopda Pujan – Payment Required</title>
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
            <td style="padding:30px; font-size:15px; line-height:1.6;">
              <p style="margin-top:0;">Dear <?= esc($first_name) ?>,</p>

              <p>
                Thank you for registering for the <strong>LCNL Chopda Pujan</strong>, taking place on
                <strong>Monday 20<sup>th</sup> October 2025</strong> at the
                <strong>Dhamecha Lohana Centre (DLC Hall), Brember Road, HA2 8AX</strong>.
              </p>

              <p>
                <strong>Your registration is currently pending.</strong>
                To confirm your place, please make a payment of <strong>£25 per pooja</strong> using the bank details below.
              </p>

              <!-- Payment Box -->
              <div style="background-color:#f7f3f5; border:1px solid #e0d0d5; border-radius:6px; padding:15px 20px; margin:25px 0;">
                <p style="margin:0 0 8px 0; font-size:15px;"><strong>Bank Details</strong></p>
                <p style="margin:0; font-size:15px;">
                  <strong>Account Name:</strong> Lohana Community North London<br>
                  <strong>Sort Code:</strong> 40&nbsp;23&nbsp;13<br>
                  <strong>Account Number:</strong> 2149&nbsp;7995<br>
                  <strong>Payment Reference:</strong> CP–<?= esc($first_name) . esc($last_name) ?><br>

                </p>
              </div>

              <p>
                Once payment has been received, you’ll receive a confirmation email.
                Please note that registration is only complete once payment has been made.
              </p>

              <!-- Event Terms -->
              <div style="background-color:#fef9f6; border:1px solid #f2e3da; border-radius:6px; padding:15px 20px; margin:30px 0;">
                <p style="margin:0 0 10px 0; font-weight:bold; color:#7a1d3c;">Event Terms & Information</p>
                <ul style="margin:0; padding-left:18px; font-size:15px; line-height:1.6;">
                  <li>All pooja samagri will be provided — please bring your own <strong>book</strong> and <strong>swaroop of Lord Ganesh</strong>.</li>
                  <li>Each pooja is priced at <strong>£25 per Yajman</strong> (maximum of <strong>2 Yajmans</strong> per pooja).</li>
                  <li><strong>Guests</strong> are welcome to attend and observe but will be seated on the side.</li>
                  <li><strong>Registration is mandatory</strong> via <a href="https://lcnl.org/events/register/chopda-pujan" style="color:#7a1d3c; text-decoration:none;">https://lcnl.org/events/register/chopda-pujan</a>.</li>
                  <li><strong>Payment must be made in advance</strong> to confirm your booking.</li>
                </ul>
              </div>

              <p>
                For any questions, please contact:<br>
                <strong>Madhuben</strong> – 07500&nbsp;701&nbsp;318<br>
                <strong>Vishal</strong> – 07732&nbsp;010&nbsp;955
              </p>

              <p style="margin-top:30px;">Kind Regards,<br><strong>LCNL Committee</strong></p>
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
