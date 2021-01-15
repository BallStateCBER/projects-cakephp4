<?php
/**
 * @var \App\View\AppView $this
 */

use Cake\Core\Configure;

$signature = $this->element('email_signature_html', [], ['ignoreMissing' => true]);
$defaultSignature = sprintf(
    '<p><br /><strong>%s<br />Center for Business and Economic Research<br />Ball State University</strong><br />' .
    '<a href="%s">%s</a></p>',
    Configure::read('DataCenter.siteTitle'),
    Configure::read('DataCenter.siteUrl'),
    Configure::read('DataCenter.siteUrl')
);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    </head>
    <body>
        <table cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family: Verdana, Helvetica, Arial;">
            <tr>
                <td bgcolor="#FFFFFF" align="center">
                    <table width="650px" cellspacing="0" cellpadding="3">
                        <tr>
                            <td>
                                <?= $this->fetch('content') ?>

                                <?= $signature ? $signature : $defaultSignature ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
