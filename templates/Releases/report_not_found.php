<?php
use Cake\Core\Configure;

$adminEmail = Configure::read('adminEmail');
?>

<p class="alert alert-danger">
    Sorry, we could not find that file. If you need assistance, please email
    <a href="mailto:<?= $adminEmail ?>"><?= $adminEmail ?></a>.
</p>
