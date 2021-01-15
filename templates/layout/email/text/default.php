<?php
/**
 * @var \App\View\AppView $this
 */

use Cake\Core\Configure;

echo $this->fetch('content');
$signature = $this->element('email_signature_text', [], ['ignoreMissing' => true]);
$defaultSignature =
    Configure::read('DataCenter.siteTitle') . "\n" .
    "Center for Business and Economic Research\n" .
    "Ball State University\n" .
    Configure::read('DataCenter.siteUrl');
?>

<?= $signature ? $signature : $defaultSignature ?>
