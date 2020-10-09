<?php
/**
 * @var \App\View\AppView $this
 * @var array $sidebarVars
 */
?>
<h2>
    Clients, Partners, and Sponsors
</h2>
<ul class="partners unstyled" id="partners-list">
    <?php foreach ($sidebarVars['partners'] as $partner) : ?>
        <li>
            <?= $this->Html->link(
                $partner->short_name,
                [
                    'controller' => 'Partners',
                    'action' => 'view',
                    'id' => $partner->id,
                    'slug' => $partner->slug,
                ],
                ['title' => $partner->name]
            ) ?>
        </li>
    <?php endforeach; ?>
</ul>

<h2>
    Topics
</h2>
<ul class="tags unstyled" id="tags-list">
    <?php foreach ($sidebarVars['tags'] as $tag) : ?>
        <li>
            <?php
                $tagName = ucwords($tag['name']);
                $tagName = str_replace(' And ', ' and ', $tagName);
                echo $this->Html->link($tagName, [
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
    Publishing Date
</h2>
<ul class="unstyled" id="years-list">
    <?php foreach ($sidebarVars['years'] as $year) : ?>
        <li>
            <?= $this->Html->link($year, [
                'controller' => 'Releases',
                'action' => 'year',
                'year' => $year,
            ]) ?>
        </li>
    <?php endforeach; ?>
</ul>

<h2>
    Search
</h2>
<?= $this->Form->create(
    null,
    [
        'method' => 'get',
        'url' => ['controller' => 'Releases', 'action' => 'search'],
    ]
) ?>
<?= $this->Form->control('term', ['label' => false]) ?>
<?= $this->Form->submit('Search') ?>
<?= $this->Form->end() ?>

<?php if ($this->request->getSession()->read('User')) : ?>
    <h2>
        Administration
    </h2>
    <ul class="unstyled">
        <li>
            <?= $this->Html->link('New Release', ['controller' => 'Releases', 'action' => 'add']) ?>
        </li>
        <li>
            <?= $this->Html->link('New User', ['controller' => 'Users', 'action' => 'add']) ?>
        </li>
        <li>
            <?= $this->Html->link('Clients / Partners / Sponsors', ['controller' => 'Partners', 'action' => 'index']) ?>
        </li>
        <li>
            <?= $this->Html->link('Tags', ['controller' => 'Tags', 'action' => 'edit']) ?>
        </li>
        <li>
            <?= $this->Html->link('Authors', ['controller' => 'Authors', 'action' => 'index']) ?>
        </li>
        <li>
            <?= $this->Html->link('Change my password', ['controller' => 'Users', 'action' => 'changePassword']) ?>
        </li>
        <li>
            <?= $this->Html->link('Logout', ['controller' => 'Users', 'action' => 'logout']) ?>
        </li>
    </ul>
<?php else : ?>
    <?= $this->Html->link(
        'Admin login',
        [
            'controller' => 'Users',
            'action' => 'login',
            'admin' => false,
            'plugin' => false,
        ],
        ['id' => 'login_link']
    ) ?>
<?php endif; ?>
