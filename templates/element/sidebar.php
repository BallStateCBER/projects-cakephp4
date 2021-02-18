<?php
/**
 * @var \App\View\AppView $this
 * @var array $sidebarVars
 */
?>
<?php if ($sidebarVars['user']): ?>
    <section class="navbar-light navbar-expand-md sidebar-collapse">
        <div class="row">
            <div class="col">
                <h2 data-toggle="collapse" data-target="#sidebar-admin">
                    Administration
                </h2>
            </div>
            <div class="col-2">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidebar-admin"
                        aria-controls="sidebar-admin" aria-expanded="false" aria-label="Toggle administration links">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </div>
        <ul class="list-unstyled collapse navbar-collapse" id="sidebar-admin">
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
    </section>
<?php endif; ?>

<section class="navbar-light navbar-expand-md sidebar-collapse">
    <div class="row">
        <div class="col">
            <h2 data-toggle="collapse" data-target="#sidebar-search">
                Search
            </h2>
        </div>
        <div class="col-2">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidebar-search"
                    aria-controls="sidebar-search" aria-expanded="false" aria-label="Toggle search form">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </div>
    <div class="collapse navbar-collapse" id="sidebar-search">
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
        <?= $this->Form->submit('Search', ['class' => 'btn btn-secondary']) ?>
        <?= $this->Form->end() ?>
    </div>
</section>

<section class="navbar-light navbar-expand-md sidebar-collapse">
    <div class="row">
        <div class="col">
            <h2 data-toggle="collapse" data-target="#tags-list">
                Topics
            </h2>
        </div>
        <div class="col-2">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#tags-list"
                    aria-controls="tags-list" aria-expanded="false" aria-label="Toggle topic links">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </div>
    <ul class="tags list-unstyled collapse navbar-collapse" id="tags-list">
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
</section>

<section class="navbar-light navbar-expand-md sidebar-collapse">
    <div class="row">
        <div class="col">
            <h2 data-toggle="collapse" data-target="#partners-list">
                Clients, Partners, and Sponsors
            </h2>
        </div>
        <div class="col-2">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#partners-list"
                    aria-controls="partners-list" aria-expanded="false"
                    aria-label="Toggle clients, partners, and sponsors links">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </div>
    <ul class="partners list-unstyled collapse navbar-collapse" id="partners-list">
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
</section>

<section class="navbar-light navbar-expand-md sidebar-collapse">
    <div class="row">
        <div class="col">
            <h2 data-toggle="collapse" data-target="#years-list">
                Publishing Date
            </h2>
        </div>
        <div class="col-2">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#years-list"
                    aria-controls="years-list" aria-expanded="false" aria-label="Toggle publishing year links">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </div>
    <ul class="list-unstyled collapse navbar-collapse" id="years-list">
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
</section>

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
