<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @if($type === 'not_exists')
            {{ __('messages.email.account_not_exists.subject') }}
        @else
            {{ __('messages.email.account_exists.subject') }}
        @endif
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { margin:0; padding:0; font-family:'Poppins',Arial,sans-serif; }
        table { border-spacing:0; }
        table td { border-collapse:collapse; }
        img { border:0; display:block; }
        a { text-decoration:none; }
    </style>
</head>
<body style="background:#ffffff; margin:auto; width:40%;font-family: serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #E0E0E0;border-radius:5px;">
    <tr>
        <td align="center">
            <!-- HEADER -->
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr><td height="40"></td></tr>
                <tr>
                    <td align="center" valign="middle">
                        <table cellpadding="0" cellspacing="0" align="center">
                            <tr>
                                <td valign="middle">
                                    <img src="https://fruitable.site/images/logo.png" alt="Fruitables" width="64" style="display:block;width:4rem;">
                                </td>
                                <td width="12"></td>
                                <td valign="middle" style="font-size:36px;font-weight:600;color:#81c408;">
                                    Fruitables
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr><td height="40"></td></tr>
            </table>

            <!-- CONTENT -->
            <table width="100%" cellpadding="0" cellspacing="0" bgcolor="#f0f0f0">
                <tr>
                    <td align="center" class="container-padding">
                        <table class="row" width="580" style="width:80%;">
                            <tr><td height="40"></td></tr>

                            <tr>
                                <td align="center">
                                    <table width="80%" cellpadding="0" cellspacing="0">
                                        {{-- Message --}}
                                        <tr>
                                            <td style="font-size:14px;line-height:22px;color:#444;">
                                                @if($type === 'not_exists')
                                                    {{ __('messages.email.account_not_exists.message') }}
                                                @else
                                                    {{ __('messages.email.account_exists.message') }}
                                                @endif
                                            </td>
                                        </tr>

                                        <tr><td height="15"></td></tr>

                                        {{-- Notice with link --}}
                                        <tr>
                                            <td style="font-size:14px;line-height:22px;color:#444;">
                                                @if($type === 'not_exists')
                                                    {{ __('messages.email.account_not_exists.notice') }}
                                                    <a href="{{ $url }}" style="color:#81c408;font-weight:500;text-decoration:underline;">
                                                        <strong>{{ __('messages.email.account_not_exists.link_text') }}</strong>
                                                    </a>
                                                    {{ __('messages.email.account_not_exists.link_suffix') }}
                                                @else
                                                    {{ __('messages.email.account_exists.notice') }}
                                                    <a href="{{ $url }}" style="color:#81c408;font-weight:500;text-decoration:underline;">
                                                        <strong>{{ __('messages.email.account_exists.link_text') }}</strong>
                                                    </a>
                                                    {{ __('messages.email.account_exists.link_suffix') }}
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td height="40">
                                    <hr style="border:none;border-top:1px solid #ddd;width:100%">
                                </td>
                            </tr>

                            {{-- Project Info --}}
                            <tr>
                                <td style="font-size:14px;line-height:22px;color:#444;">
                                    {!! __('messages.email.complete_action.project_info_text') !!}
                                </td>
                            </tr>

                            <tr><td height="15"></td></tr>

                            <tr>
                                <td align="center">
                                    <a href="https://fruitable.site/about" style="display:inline-block;padding:10px 22px;background:#f5f5f5;color:#81c408;border:1px solid #81c408;border-radius:4px;font-size:14px;font-weight:500;">
                                        @if($type === 'not_exists')
                                            {{ __('messages.email.account_not_exists.project_info_button') }}
                                        @else
                                            {{ __('messages.email.account_exists.project_info_button') }}
                                        @endif
                                    </a>
                                </td>
                            </tr>

                            <tr><td height="40"></td></tr>

                            {{-- Footer --}}
                            <tr>
                                <td style="font-size:13px;line-height:20px;color:#777;">
                                    @if($type === 'not_exists')
                                        {{ __('messages.email.account_not_exists.footer_text') }}
                                    @else
                                        {{ __('messages.email.account_exists.footer_text') }}
                                    @endif
                                </td>
                            </tr>

                            <tr><td height="40"></td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
