<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubuntu 360</title>

    <style>
        body {
            margin: 0;
            padding: 0;
        }

        @media only screen and (max-width: 599px) {
            table[class=cc_main_table] {
                width: 100% !important;
            }
        }
    </style>
</head>

<body bgcolor="#f7f7f7">

    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#f7f7f7">
        <tr>
            <td align="center">
                <table width="600" cellspacing="0" cellpadding="0" class="cc_main_table" bgcolor="#fff">
                    <tr>
                        <td style="background-color: #edf2f7;padding: 20px;" align="center">
                            <img src="{{ url('/public/uploads/image/logo.png') }}" alt="Ubuntu 360 123" width="150px">
                        </td>
                    </tr>
                    {!! $mailData['message'] !!}
                    <tr>
                        <td style="background-color: #edf2f7;padding: 30px;font-size: 12px;color: #b0adc5;" align="center">© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
