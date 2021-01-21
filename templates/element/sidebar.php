<?php
/**
 * @var \App\View\AppView $this
 * @var array $sidebarVars
 */
?>
<?php if ($sidebarVars['user']): ?>
    <h2>
        Administration
    </h2>
    <ul class="unstyled">
        <li>
            <?= $this->Html->link(
                'New Release',
                [
                    'plugin' => false,
                    'controller' => 'Releases',
                    'action' => 'add',
                ]
            ) ?>
        </li>
        <li>
            <?= $this->Html->link(
                'Releases',
                [
                    'plugin' => false,
                    'controller' => 'Releases',
                    'action' => 'admin',
                ]
            ) ?>
        </li>
        <li>
            <?= $this->Html->link(
                'Users',
                [
                    'plugin' => false,
                    'controller' => 'Users',
                    'action' => 'index',
                ]
            ) ?>
        </li>
        <li>
            <?= $this->Html->link(
                'Partners',
                [
                    'plugin' => false,
                    'controller' => 'Partners',
                    'action' => 'index',
                ]
            ) ?>
        </li>
        <li>
            <?= $this->Html->link(
                'Tags',
                [
                    'plugin' => false,
                    'controller' => 'Tags',
                    'action' => 'index',
                ]
            ) ?>
        </li>
        <li>
            <?= $this->Html->link(
                'Authors',
                [
                    'plugin' => false,
                    'controller' => 'Authors',
                    'action' => 'index',
                ]
            ) ?>
        </li>
        <li>
            <?= $this->Html->link(
                'Update login info',
                [
                    'plugin' => false,
                    'controller' => 'Users',
                    'action' => 'edit',
                    $sidebarVars['user']['id'],
                ]
            ) ?>
        </li>
        <li>
            <?= $this->Html->link(
                'Logout',
                [
                    'plugin' => false,
                    'controller' => 'Users',
                    'action' => 'logout',
                ]
            ) ?>
        </li>
    </ul>
<?php endif; ?>

<h2>
    Search
</h2>
<?= $this->Form->create(
    null,
    [
        'method' => 'get',
        'url' => [
            'plugin' => false,
            'controller' => 'Releases',
            'action' => 'search',
        ],
    ]
) ?>
<?= $this->Form->control('term', ['label' => false]) ?>
<?= $this->Form->submit('Search') ?>
<?= $this->Form->end() ?>

<h2>
    Topics
</h2>
<ul class="tags unstyled" id="tags-list">
    <?php foreach ($sidebarVars['tags'] as $tag): ?>
        <li>
            <?php
                echo $this->Html->link($tag->uc_name, [
                    'plugin' => false,
                    'controller' => 'Tags',
                    'action' => 'view',
                    'id' => $tag->id,
                    'slug' => $tag->slug,
                ]);
            ?>
        </li>
    <?php endforeach; ?>
</ul>

<h2>
    Clients, Partners, and Sponsors
</h2>
<ul class="partners unstyled" id="partners-list">
    <?php foreach ($sidebarVars['partners'] as $partner): ?>
        <li>
            <?= $this->Html->link(
                $partner->short_name,
                $partner->url,
                ['title' => $partner->name]
            ) ?>
        </li>
    <?php endforeach; ?>
</ul>

<h2>
    Publishing Date
</h2>
<ul class="unstyled" id="years-list">
    <?php foreach ($sidebarVars['years'] as $year): ?>
        <li>
            <?= $this->Html->link($year, [
                'plugin' => false,
                'controller' => 'Releases',
                'action' => 'year',
                'year' => $year,
            ]) ?>
        </li>
    <?php endforeach; ?>
</ul>

<?php if (!$sidebarVars['user']): ?>
    <?= $this->Html->link(
        'Admin login',
        [
            'plugin' => false,
            'controller' => 'Users',
            'action' => 'login',
        ],
        ['id' => 'login_link']
    ) ?>
<?php endif; ?>
