<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>New Chopda Pujan Registration – LCNL</title>
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
            <td style="padding:30px; font-size:15px; line-height:1.6;">
              <p style="margin-top:0;"><strong>New Chopda Pujan Registration Received</strong></p>

              <p>
                A new registration has been submitted for the <strong>LCNL Chopda Pujan 2025</strong>.
                The participant’s details are as follows:
              </p>

              <!-- Registration Details Table -->
              <table role="presentation" cellpadding="8" cellspacing="0" border="0" width="100%" 
                     style="border-collapse:collapse; border:1px solid #e0d0d5; border-radius:6px; background-color:#fdfbfa; margin-bottom:25px;">
                <tr style="background-color:#f7f3f5;">
                  <td width="35%" style="font-weight:bold;">Name</td>
                  <td><?= esc($first_name) ?> <?= esc($last_name) ?></td>
                </tr>
                <tr>
                  <td style="font-weight:bold;">Email</td>
                  <td><?= esc($email) ?></td>
                </tr>
                <tr>
                  <td style="font-weight:bold;">Phone</td>
                  <td><?= esc($phone) ?></td>
                </tr>
                <tr>
                  <td style="font-weight:bold;">No. of Participants</td>
                  <td><?= esc($num_participants) ?></td>
                </tr>
                <tr>
                  <td style="font-weight:bold;">No. of Guests</td>
                  <td><?= esc($num_guests) ?></td>
                </tr>
                <tr>
                  <td style="font-weight:bold;">Notes</td>
                  <td><?= esc($notes ?: 'N/A') ?></td>
                </tr>
                <tr>
                  <td style="font-weight:bold;">Status</td>
                  <td><span style="color:#7a1d3c; font-weight:bold;"><?= ucfirst($status ?? 'pending') ?></span></td>
                </tr>
              </table>

              <!-- Payment Info -->
              <div style="background-color:#f7f3f5; border:1px solid #e0d0d5; border-radius:6px; padding:15px 20px; margin:25px 0;">
                <p style="margin:0 0 8px 0; font-size:15px;"><strong>Payment Reminder (for reference)</strong></p>
                <p style="margin:0; font-size:15px;">
                  <strong>Amount:</strong> £25 per pooja<br>
                  <strong>Account Name:</strong> Lohana Community North London<br>
                  <strong>Sort Code:</strong> 40&nbsp;23&nbsp;13<br>
                  <strong>Account Number:</strong> 2149&nbsp;7995<br>
                  <strong>Payment Reference:</strong> CP–<?= esc($first_name) . esc($last_name) ?><br>
                  
                </p>
              </div>

              <!-- Event Notes -->
              <div style="background-color:#fef9f6; border:1px solid #f2e3da; border-radius:6px; padding:15px 20px; margin:30px 0;">
                <p style="margin:0 0 10px 0; font-weight:bold; color:#7a1d3c;">Event Terms & Information</p>
                <ul style="margin:0; padding-left:18px; font-size:15px; line-height:1.6;">
                  <li>All pooja samagri will be provided — please bring your own <strong>book</strong> and <strong>swaroop of Lord Ganesh</strong>.</li>
                  <li>Each pooja is priced at <strong>£25 per Yajman</strong> (maximum of <strong>2 Yajmans</strong> per pooja).</li>
                  <li><strong>Guests</strong> are welcome to attend and observe but will be seated on the side.</li>
                  <li><strong>Registration is mandatory</strong> via <a href="https://lcnl.org/events/register/chopda-pujan" style="color:#7a1d3c; text-decoration:none;">https://lcnl.org/events/register/chopda-pujan</a>.</li>
                  <li><strong>Payment must be made in advance</strong> to confirm the booking.</li>
                </ul>
              </div>

              <p>
                This registration has been automatically logged in the LCNL event registrations database.<br>
                You may verify it from the admin dashboard when available.
              </p>

              <p style="margin-top:30px;">Kind Regards,<br><strong>LCNL Website System</strong></p>
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
