<?php
/**
 * @var \App\View\AppView $this
 */
$this->extend('DataCenter.default');
$this->assign('sidebar', $this->element('sidebar'));
?>

<?php $this->append('scriptBottom'); ?>
<?php $this->end(); ?>

<div id="content">
    <div id="cri_main">
        <?= $this->fetch('content') ?>
    </div>
</div>
