<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Bel</title>
    <link
        href="https://fonts.googleapis.com/css?family=Lato:100,100i,300,300i,400,400i,700,700i,900,900i&amp;subset=latin-ext"
        rel="stylesheet">
</head>

<body style="margin: 0; padding: 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="600px" id="bodyTable"
       style="box-shadow: 0px 0px 6px #00000029;margin: 0 auto;max-width: 600px;min-width: 600px;">
    <!-- header -->
    <tr>
        <td align="center" valign="top">
            <table border="0" cellpadding="0" cellspacing="0" width="600px" id="emailContainer">
                <tr>
                    <td align="center" valign="top" style="padding-top: 50px;padding-bottom: 30px;">
                        <a href="https://belenergise.com/"><img src="{{ asset('assets/images/email/email_logo.png')}}"
                                                                alt="logo"></a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td align="center" valign="top">
            <table border="0" cellpadding="0" cellspacing="0" width="600px" id="emailContainer"
                   style="margin-bottom: 10px;margin-top: 50px;">
                <tr>
                    <td align="center" valign="top" style="width: 330px;float: left; padding-bottom: 30px;">
                        <h2
                            style="color: #03258b; margin: 0;font-family: 'Helvetica',sans-serif;font-weight: 800;text-align: left;padding-left: 62px;">
                            PASSWORD RESET REQUEST
                        </h2>
                        <h6
                            style="color: #5b7ce0  !important;margin: 0;font-family: 'Helvetica',sans-serif;font-weight:300;text-align: left;padding-left: 62px;margin-top: 30px;margin-bottom: 15px;">
                            Click the button to change your password</h6>
                        <a title="Signup Now" href="{{$url}}" target="_blank" rel="noopener"
                           style="text-decoration: none;font-size: 12px;color: #fff;font-family: 'Helvetica',sans-serif;background: #778ff9;display: block;width: 200px;height: 40px;line-height: 40px;font-weight: bolder;margin: 0;border-radius: 0px;">Reset
                            Password</a>
                    </td>
                    <td align="center" valign="top"
                        style=" width: 270px;float: left; display: flex;justify-content: center;align-items: center;flex-direction: column;">
                        <img src="{{ asset('assets/images/email/email_em.png')}}" alt="">

                    </td>
                </tr>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td align="center" valign="top">
            <table border="0" cellpadding="0" cellspacing="0" width="600px" id="emailContainer"
                   style="margin-bottom: 10px;">
                <tr>
                    <td align="center" valign="top" style="background: #e5f7ff;"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td align="center" valign="top">
            <table border="0" cellpadding="0" cellspacing="0" width="600px" id="emailContainer"
                   style="padding: 0 0;background: #e5f7ff;width: 349px;border-bottom-right-radius: 100px;border-bottom-left-radius: 100px;margin-top: 20px;margin-bottom: 40px;">
                <tr>
                    <td align="center" valign="top" style="display: flex; padding: 0 35px;">
                        <h5 style="background: #e5f7ff;">
                        </h5>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td align="center" valign="top">
            <table border="0" cellpadding="0" cellspacing="0" width="px" id="emailContainer"
                   style="padding: 10PX 0;background: #ffffff;">
                <tr>
                    <td align="center" valign="top" style="display: flex; padding: 0 35px;">
                        <h5
                            style="margin: 0 0 30px 0;font-size: 14px;font-family: 'Helvetica',sans-serif;color: #778ff9;font-weight: 400;width: 100%;line-height: 22px;">
                            This link is valid for for 2 hours.<br>
                            If you did not request password reset then ignore this email.
                        </h5>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</body>

</html>
