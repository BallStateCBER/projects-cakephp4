<?php
/**
 * @var \App\Model\Entity\Release[] $releases
 * @var \App\Model\Entity\Tag[] $tags
 * @var \App\View\AppView $this
 * @var string $searchTerm
 */

$count = $this->Paginator->counter('{{count}}');
?>
<?php if ($searchTerm): ?>
    <h1 class="page_title">
        <?= sprintf(
            '%s %s for "%s"',
            $count,
            __n('Result', 'Results', count($releases)),
            $searchTerm
        ) ?>
    </h1>

    <?php if ($tags): ?>
        <p class="search_results_tags">
            <?php
            $tagList = [];
            foreach ($tags as $tag) {
                $tagList[] = $this->Html->link(
                    ucwords($tag->name),
                    [
                        'controller' => 'Tags',
                        'action' => 'view',
                        'id' => $tag->id,
                        'slug' => $tag->slug,
                    ]
                );
            }
            ?>
            You can also try browsing projects and publications with
            <?= __n('the tag', 'these tags', count($tagList)) ?>:
            <?= implode(', ', $tagList) ?>
        </p>
    <?php endif; ?>

    <?php if ($releases): ?>
        <table class="releases-list table">
            <?php foreach ($releases as $release): ?>
                <tr>
                    <td>
                        <?= $release->released->format('F j, Y') ?>
                    </td>
                    <td>
                        <?= $this->Html->link(
                            $this->Text->highlight(
                                $release->title,
                                $searchTerm,
                                ['format' => '<strong>\1</strong>']
                            ),
                            [
                                'controller' => 'Releases',
                                'action' => 'view',
                                'id' => $release->id,
                                'slug' => $release->slug,
                            ],
                            [
                                'class' => 'title',
                                'escape' => false,
                            ]
                        ) ?>
                        <p>
                            <?php
                                $description = strip_tags($release->description);
                                if (stripos($description, $searchTerm) === false) {
                                    echo $this->Text->truncate($description);
                                } else {
                                    $description = $this->Text->highlight(
                                        $description,
                                        $searchTerm,
                                        ['format' => '<strong>\1</strong>']
                                    );
                                    echo $this->Text->excerpt($description, $searchTerm);
                                }
                            ?>
                        </p>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <?= $this->element('DataCenter.pagination') ?>
    <?php else: ?>
        <p>
            No results found
        </p>
    <?php endif; ?>
<?php else: ?>
    <h1 class="page_title">
        Search
    </h1>

    <p>
        Please enter a search term to find related projects and publications.
    </p>
<?php endif; ?>
