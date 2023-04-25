<?php
/**
 * Customer completed order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-completed-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails\HTML
 * @version 3.7.0
 */

defined('ABSPATH') || exit;
?>

<table style="box-sizing:border-box;max-width:600px" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
    <tbody style="box-sizing:border-box">
    <tr style="box-sizing:border-box"></tr>
    <tr style="box-sizing:border-box">
        <td style="box-sizing:border-box;padding:0px 35px 20px;background-color:#ffffff;font-size:16px;color:#000000" bgcolor="rgb(255, 255, 255)" align="center">
            <div style="box-sizing:border-box;margin:0px 0px 20px;font:16px Arial,sans-serif"></div>
            <table style="box-sizing:border-box;width:100%;border-collapse:collapse;background:none 0% 0% repeat scroll #ececec" width="100%">
                <tbody style="box-sizing:border-box">
                <tr style="box-sizing:border-box"></tr>
                </tbody>
            </table>
            <table style="box-sizing:border-box;width:100%;border-collapse:collapse;background:none 0% 0% repeat scroll #ececec" width="100%">
                <tbody style="box-sizing:border-box">
                <tr style="box-sizing:border-box"></tr>
                </tbody>
            </table>
            <a style="box-sizing:border-box;display:inline-block;padding:5px;min-height:50px;min-width:50px" href="https://www.mediamarkt.nl/"
               rel="noopener noreferrer" target="_blank"><img
                    style="box-sizing:border-box;width:233px;height:32px;text-align:left;padding:0px;margin:0px"
                    src="https://somashome.be/wp-content/uploads/2022/08/Somas-Home-Logo-png.png"
                    width="233" height="32" class="CToWUd" data-bit="iit"></a>
            <table style="box-sizing:border-box;width:100%;margin-top:10px;margin-bottom:10px" width="100%">
                <tbody style="box-sizing:border-box">
                <tr style="box-sizing:border-box">
                    <td style="box-sizing:border-box;background-color:rgba(0,0,0,0.1);height:1px" height="1" bgcolor="rgba(0, 0, 0, 0.1)"></td>
                </tr>
                </tbody>
            </table>
            <span style="box-sizing:border-box;margin:0px 0px 20px;font:16px Arial,sans-serif"><font style="vertical-align: inherit;"><font
                        style="vertical-align: inherit;">Order number: </font></font><b style="box-sizing:border-box"><font
                        style="vertical-align: inherit;"><font
                            style="vertical-align: inherit;"><?= $order->get_order_number() ?></font></font></b><b
                    style="box-sizing:border-box"> </b></span>
            <table style="box-sizing:border-box;width:100%;margin-top:10px;margin-bottom:10px" width="100%">
                <tbody style="box-sizing:border-box">
                <tr style="box-sizing:border-box">
                    <td style="box-sizing:border-box;background-color:rgba(0,0,0,0.1);height:1px" height="1"
                        bgcolor="rgba(0, 0, 0, 0.1)"></td>
                </tr>
                </tbody>
            </table>
            <div style="box-sizing:border-box;margin:0px 0px 20px;font:16px Arial,sans-serif;text-align:left">
                <div style="box-sizing:border-box;color:#212121;line-height:25px"><font
                        style="vertical-align: inherit;"><font style="vertical-align: inherit;">Best
                            Bags,</font></font><br>
                    <div style="box-sizing:border-box"></div>
                </div>
                <div style="box-sizing:border-box"><font
                        style="vertical-align: inherit;"><font style="vertical-align: inherit;">Your order has been
                            given to the delivery person. </font><font style="vertical-align: inherit;">You will be
                            informed as soon as it is on its way.</font></font></div>
                <div style="box-sizing:border-box"></div>
                <div style="box-sizing:border-box"><a
                        style="box-sizing:border-box;color:#0066cc;line-height:25px;font-weight:600;text-decoration:none;font-size:12px"
                        href="#" rel="noopener noreferrer"
                        target="_blank"><font
                            style="vertical-align: inherit;"><font style="vertical-align: inherit;">Track your
                                package</font></font></a></div>
            </div>
            <table style="box-sizing:border-box;width:100%;margin-top:10px;margin-bottom:10px" width="100%">
                <tbody style="box-sizing:border-box">
                <tr style="box-sizing:border-box"></tr>
                </tbody>
            </table>
            <div style="box-sizing:border-box;margin:0px 0px 20px;font:16px Arial,sans-serif;text-align:left;text-decoration:none">
                <b style="box-sizing:border-box"><font
                        style="vertical-align: inherit;"><font style="vertical-align: inherit;">This is on the
                            way</font></font></b></div>
            <table style="box-sizing:border-box;font-family:Arial,sans-serif;border-spacing:0px;border-collapse:collapse;border-style:solid none;width:100%;border-top-width:1px;border-top-color:#dcdcdc;border-bottom-width:1px;border-bottom-color:#dcdcdc;padding-top:0px;margin-bottom:20px"
                   width="100%">
                <thead style="box-sizing:border-box">
                <tr style="box-sizing:border-box">
                    <th style="box-sizing:border-box;border-bottom:1px solid #e9e9e9;font-family:Arial,sans-serif;text-align:left;padding:8px 10px 8px 0px;margin-bottom:0px;white-space:nowrap"
                        align="left"><span style="box-sizing:border-box"><font style="vertical-align: inherit;"><font
                                    style="vertical-align: inherit;">Number</font></font></span></th>
                    <th style="box-sizing:border-box;border-bottom:1px solid #e9e9e9;font-family:Arial,sans-serif;text-align:left;padding:8px 10px 8px 0px;margin-bottom:0px;white-space:nowrap"
                        align="left"><span style="box-sizing:border-box"><font style="vertical-align: inherit;"><font
                                    style="vertical-align: inherit;">Description</font></font></span></th>
                </tr>
                </thead>
                <tbody style="box-sizing:border-box">
                <tr style="box-sizing:border-box">
                    <td style="box-sizing:border-box;border-bottom:1px solid #e9e9e9;font-family:Arial,sans-serif;text-align:left;padding:8px 20px 8px 0px;vertical-align:top"
                        valign="top" align="left"><span
                            style="box-sizing:border-box"><font
                                style="vertical-align: inherit;"><font
                                    style="vertical-align: inherit;">3</font></font></span></td>
                    <td
                        style="box-sizing:border-box;border-bottom:1px solid #e9e9e9;font-family:Arial,sans-serif;text-align:left;padding:8px 20px 8px 0px;vertical-align:top"
                        valign="top" align="left"><span
                            style="box-sizing:border-box"><font
                                style="vertical-align: inherit;"><font style="vertical-align: inherit;">NINTENDO Switch OLED - Wit</font></font></span>
                    </td>
                </tr>
                </tbody>
                <tbody style="box-sizing:border-box">
                <tr style="box-sizing:border-box"></tr>
                <tr style="box-sizing:border-box"></tr>
                <tr style="box-sizing:border-box">
                    <td
                        style="box-sizing:border-box;font-family:Arial,sans-serif;text-align:right;padding:0px"
                        align="right"><span style="box-sizing:border-box"></span></td>
                </tr>
                <tr  style="box-sizing:border-box"></tr>
                </tbody>
            </table>
            <div
                style="box-sizing:border-box;margin:0px 0px 20px;font:16px Arial,sans-serif;text-align:left">
                <div style="box-sizing:border-box;color:#212121"></div>
                <div style="box-sizing:border-box;color:#212121">
                    <div style="box-sizing:border-box"></div>
                </div>
                <div
                    style="box-sizing:border-box;color:#212121;line-height:25px"><font
                        style="vertical-align: inherit;"><font style="vertical-align: inherit;">If your order
                            consists of several products, it is possible that we split the shipment. </font><font
                            style="vertical-align: inherit;">When this happens, you will be informed. </font><font
                            style="vertical-align: inherit;">You will receive a separate invoice for each
                            delivery.</font></font></div>
            </div>
            <table
                style="box-sizing:border-box;width:100%;margin-top:10px;margin-bottom:10px" width="100%">
                <tbody style="box-sizing:border-box">
                <tr style="box-sizing:border-box"></tr>
                </tbody>
            </table>
            <div
                style="box-sizing:border-box;margin:0px 0px 20px;font:16px/25px Arial,sans-serif;text-align:left;color:#000000">
                <b
                    style="box-sizing:border-box;color:#212121"><font style="vertical-align: inherit;"><font
                            style="vertical-align: inherit;">Secure delivery</font></font><br></b>
                <div
                    style="box-sizing:border-box;line-height:25px;color:#000000">
                    <div style="box-sizing:border-box"><font
                            style="vertical-align: inherit;"><font style="vertical-align: inherit;">For the safety
                                of you and our deliverers, the delivery is different than you are used to. </font><font
                                style="vertical-align: inherit;">We work with multiple carriers, if verification is
                                required, this will be communicated to you from the carrier. </font><font
                                style="vertical-align: inherit;">Please have the carrier's email ready on the day of
                                delivery.</font></font></div>
                    <div style="box-sizing:border-box"></div>
                </div>
                <div  style="box-sizing:border-box"></div>
                <div  style="box-sizing:border-box"><b
                        style="box-sizing:border-box"><font
                            style="vertical-align: inherit;"><font style="vertical-align: inherit;">What if I'm not
                                at home?</font></font></b></div>
                <div
                    style="box-sizing:border-box;line-height:25px"><font style="vertical-align: inherit;"><font
                            style="vertical-align: inherit;">The delivery person will then deliver the package to
                            the neighbors (with a value of up to 100 euros), in your mailbox or at a parcel point in
                            your area. </font><font style="vertical-align: inherit;">You will receive a card with
                            information about the location of your package. </font><font
                            style="vertical-align: inherit;">Always keep an eye on the carrier's email to stay
                            informed about the status of your order.&nbsp;</font></font></div>
                <div  style="box-sizing:border-box"></div>
                <div  style="box-sizing:border-box"><b
                        style="box-sizing:border-box"><font
                            style="vertical-align: inherit;"><font style="vertical-align: inherit;">Can I still
                                change the delivery time or delivery address?</font></font></b></div>
                <div
                    style="box-sizing:border-box;line-height:25px"><font style="vertical-align: inherit;"><font
                            style="vertical-align: inherit;">You can no longer change the delivery time or delivery
                            address with us. </font><font style="vertical-align: inherit;">View the delivery options of
                            the carrier via the track &amp; trace above.</font></font></div>
                <div style="box-sizing:border-box"></div>
                <div  style="box-sizing:border-box"><b
                        style="box-sizing:border-box"><font
                            style="vertical-align: inherit;"><font style="vertical-align: inherit;">Still not
                                satisfied with your product?</font></font></b></div>
                <div
                    style="box-sizing:border-box;line-height:25px"><font style="vertical-align: inherit;"><font
                            style="vertical-align: inherit;">At MediaMarkt you have no less than 30 days to change
                            your mind. </font><font style="vertical-align: inherit;">Simply return your product(s) by
                            post, in one of our stores or have it picked up at your home.</font></font></div>
                <a
                    style="box-sizing:border-box;color:#636363;text-decoration:none;font-size:12px;font-weight:bold"
                    href="https://www.mediamarkt.nl/nl/service/klantenservice/ruilen-retourneren"
                    rel="noopener noreferrer" target="_blank"><font
                        style="vertical-align: inherit;"><font style="vertical-align: inherit;">To return conditions
                            ➜</font></font></a>
                <div style="box-sizing:border-box"></div>
                <div  style="box-sizing:border-box"><font
                        style="vertical-align: inherit;"><font
                            style="vertical-align: inherit;">Sincerely,</font></font></div>
                <div  style="box-sizing:border-box"></div>
                <div  style="box-sizing:border-box"><font
                        style="vertical-align: inherit;"><font style="vertical-align: inherit;">Media market</font></font>
                </div>
            </div>
            <table
                style="box-sizing:border-box;width:100%;max-width:580px" width="100%" cellspacing="0" cellpadding="0"
                border="0" align="center">
                <tbody  style="box-sizing:border-box">
                <tr  style="box-sizing:border-box">
                    <td
                        style="box-sizing:border-box;font-family:Arial;font-weight:600;font-size:16px;text-align:center;color:black;line-height:25px;text-decoration:none"
                        colspan="5" valign="top" height="30" align="center"><font style="vertical-align: inherit;"><font
                                style="vertical-align: inherit;">Did we inform you well with this
                                e-mail?</font></font></td>
                </tr>
                <tr  style="box-sizing:border-box">
                    <td
                        style="box-sizing:border-box;width:100%;max-width:580px" colspan="5" width="100%"
                        height="20"></td>
                </tr>
                <tr  style="box-sizing:border-box">
                    <td
                        style="box-sizing:border-box;padding-left:15px" width="64" align="right"><a
                            style="box-sizing:border-box"
                            href="#"
                            rel="noopener noreferrer" target="_blank"><img style="box-sizing:border-box"
                                src="https://ci3.googleusercontent.com/proxy/IcxPnWSRV-UfveNbMWQJUHGY5K1naL5fwhuQx5Xs9WO2SceKmQqYOzNqKZiSO3HlZHIPJQbd8TQkFhm1f1SfXp_mCZubIJ0ftdZ2Hg=s0-d-e1-ft#https://feedback-static.closealert.com/mail/pos_thumb.png"
                                alt="Yes, of course" width="64" height="64" border="0" class="CToWUd"
                                data-bit="iit"></a></td>
                    <td  style="box-sizing:border-box" width="20"
                         height="64"></td>
                    <td
                        style="box-sizing:border-box;padding-right:15px" width="64" align="left"><a
                            style="box-sizing:border-box"
                            href="#"
                            rel="noopener noreferrer" target="_blank"
                        ><img

                                style="box-sizing:border-box"
                                src="https://ci4.googleusercontent.com/proxy/uCHa2SlmeUUjkn4JPoX9Cm0MsJOQ8D11bLKVpXs8bX9-3XXLySZyLEQkyHDUzXR6Kqz1eJ6o6789PLSPmnGn2T5hRwgc_1-6vaC3KA=s0-d-e1-ft#https://feedback-static.closealert.com/mail/neg_thumb.png"
                                alt="Could be better" width="64" height="64" border="0" class="CToWUd"
                                data-bit="iit"></a></td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr  style="box-sizing:border-box"></tr>
    </tbody>
</table>
<?php
do_action( 'woocommerce_email_footer', $email );
?>