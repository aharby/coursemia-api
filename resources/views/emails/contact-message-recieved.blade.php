<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contact Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); padding: 30px;">
                    <tr>
                        <td style="text-align: center;">
                            <h2 style="color: #333333; margin-bottom: 10px;">Thank You for Contacting Us</h2>
                            <p style="color: #555555; font-size: 16px;">Hi {{ $data['name'] }},</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 10px;">
                            <p style="color: #555555; font-size: 15px;">We’ve received your message and our team will get back to you as soon as possible.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px; background-color: #f1f1f1; border-left: 4px solid #007bff; margin: 20px 0;">
                            <p style="font-style: italic; color: #333333;">"{{ $data['message'] }}"</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="color: #555555; font-size: 15px;">Best regards,<br><strong>Your Support Team</strong></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 30px; border-top: 1px solid #ddd;">
                            <p style="color: #999999; font-size: 12px; text-align: center;">
                                If you did not send this message, please ignore it.<br>
                                This is an automated response — please do not reply.
                            </p>
                        </td>
                    </tr>
                </table>
                <p style="font-size: 11px; color: #cccccc; margin-top: 20px;">© {{ date('Y') }} Coursemia. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>
</html>
