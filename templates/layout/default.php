<?php
/**
 * @var \App\View\AppView $this
 */
$this->extend('DataCenter.default');
$this->assign('sidebar', $this->element('sidebar'));
?>

<?php $this->append('site_title'); ?>
    <h1>
        <a href="/">
            <img src="/img/banner.jpg" alt="Projects and Publications" style="width: 1140px;" />
        </a>
    </h1>
<?php $this->end(); ?>

<?= $this->fetch('content') ?>
