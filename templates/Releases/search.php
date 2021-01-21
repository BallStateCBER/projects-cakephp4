<?php
/**
 * @var \App\Model\Entity\Release[]|\Cake\Collection\CollectionInterface $releases
 * @var \App\Model\Entity\Tag[]|\Cake\Collection\CollectionInterface $tags
 * @var \App\View\AppView $this
 * @var string $searchTerm
 */

$count = $releases ? $this->Paginator->counter('{{count}}') : 0;
?>
<?php if ($searchTerm): ?>
    <h2>
        <?= sprintf(
            '%s %s',
            $count,
            __n('Result', 'Results', count($releases))
        ) ?>
    </h2>

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
    <p>
        Please enter a search term to find related projects and publications.
    </p>
<?php endif; ?>
