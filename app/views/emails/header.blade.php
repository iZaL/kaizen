    @section('header')
        <!-- header table -->
        <table border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse:collapse;background-color:#dddad9;border-top:0;border-bottom:0">
            <tbody><tr>
                <td valign="top" style="padding-top:9px"><table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                        <tbody>
                        <tr>
                            <td valign="top">

                                <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse:collapse">
                                    <tbody><tr>

                                        <td valign="top" style="padding-top:9px;padding-right:18px;padding-bottom:9px;padding-left:18px;color:#ffffff;font-family:Helvetica;font-size:11px;line-height:125%;text-align:center">

                                            <span style="font-family:arial,helvetica,sans-serif"><span style="color:#ffffff"><span style="font-size:12pt"><span class="aBn" data-term="goog_621291290" tabindex="0"><span class="aQJ">{{ Carbon::now() }}</span></span></span></span></span>
                                        </td>
                                    </tr>
                                    </tbody></table>

                            </td>
                        </tr>
                        </tbody>
                    </table><table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#dddad9;border-collapse:collapse">
                        <tbody>
                        <tr>
                            <td style="padding:1px 18px">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                                    <tbody><tr>
                                        <td>
                                            <span></span>
                                        </td>
                                    </tr>
                                    </tbody></table>
                            </td>
                        </tr>
                        </tbody>
                    </table></td>
            </tr>
            </tbody></table>


    </td>
</tr>
<tr>
    <td align="center" valign="top">

        <table border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse:collapse;background-color:#ffffff;border-top:0;border-bottom:0">
            <tbody><tr>
                <td valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                        <tbody>
                        <tr>
                            <td valign="top" style="padding:9px">
                                <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse">
                                    <tbody><tr>
                                        <td valign="top" style="padding-right:9px;padding-left:9px;padding-top:0;padding-bottom:0;text-align:center">

                                            <a href="{{ URL::action('HomeController@index') }}" title="" style="word-wrap:break-word" target="_blank">
                                                <img align="center" alt="" src="http://kaizen.company/images/Logo.png" width="100%" style="max-width:400px;padding-bottom:0;display:inline!important;vertical-align:bottom;border:0;outline:none;text-decoration:none">
                                            </a>

                                        </td>
                                    </tr>
                                    </tbody></table>
                            </td>
                        </tr>
                        </tbody>
                    </table><table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#dddad9;border-collapse:collapse">
                        <tbody>
                        <tr>
                            <td style="padding:2px 18px 0px">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                                    <tbody><tr>
                                        <td>
                                            <span></span>
                                        </td>
                                    </tr>
                                    </tbody></table>
                            </td>
                        </tr>
                        </tbody>
                    </table><table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                        <tbody>
                        <tr>
                            <td valign="top">

                                <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse:collapse">
                                    <tbody><tr>

                                        <td valign="top" style="padding:9px 18px;color:#a9a9a9;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;font-size:13px;font-weight:bold;text-align:center;line-height:150%">

                                            <table style="width:100%;text-align:center;background-color:white;border-collapse:collapse;border:none;font-weight:bold;font-family:arial;font-size:13px">
                                                <tbody>
                                                <tr>
                                                    <td style="border-right:2px solid #dddad9;height:40px"><a href="{{ URL::route('home') }}" style="display:inline-block;width:100%;text-decoration:none;color:#a9a9a9;word-wrap:break-word;font-weight:normal" title="main page" target="_blank">Our Events</a></td>
                                                    <td style="border-right:2px solid #dddad9;height:40px"><a href="{{ URL::route('blog.index') }}" style="display:inline-block;width:100%;text-decoration:none;color:#a9a9a9;word-wrap:break-word;font-weight:normal" title="our blog" target="_blank">Our Blog</a></td>
                                                    <td><a href="{{ URL::route('contact.index') }}" style="display:inline-block;width:100%;text-decoration:none;color:#a9a9a9;word-wrap:break-word;font-weight:normal" title="contact us" target="_blank">Contact Us</a></td>
                                                </tr>
                                                </tbody>
                                            </table>

                                        </td>
                                    </tr>
                                    </tbody></table>

                            </td>
                        </tr>
                        </tbody>
                    </table></td>
            </tr>
            </tbody></table>
            <!-- end of header table -->
            @stop
