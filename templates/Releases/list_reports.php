<?php
/**
 * @var array $filesAlphabetic
 * @var array $filesNewest
 */
?>

<button class="close">
    Cancel
</button>
<button class="refresh">
    <i class="fas fa-spinner fa-spin loading" style="display: none;"></i>
    Refresh
</button>

<?php if ($filesNewest): ?>
    <strong>Select a report to link this graphic to</strong>
    <span class="sorting_options">
        Sort:
        <button class="newest selected">Newest</button>
        <button class="alphabetic">Alphabetic</button>
    </span>
    <ul class="unstyled newest">
        <?php foreach ($filesNewest as $timestamp => $info): ?>
            <li>
                <a href="#" class="report">
                    <?= $info['filename'] ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <ul class="unstyled alphabetic" style="display: none;">
        <?php foreach ($filesAlphabetic as $filename => $info): ?>
            <li>
                <a href="#" class="report">
                    <?= $info['filename'] ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    No reports have been uploaded.
<?php endif; ?>
