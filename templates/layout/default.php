<?php
/**
 * @var \App\View\AppView $this
 */
$this->extend('DataCenter.default');
$this->assign('sidebar', $this->element('sidebar'));
?>

<?php $this->append('subsite_title'); ?>
    <h1 id="subsite_title" class="max_width">
        <a href="/">
            <img src="/img/banner.jpg" alt="Projects and Publications" />
        </a>
    </h1>
<?php $this->end(); ?>

<div id="content">
    <?= $this->fetch('content') ?>
</div>
