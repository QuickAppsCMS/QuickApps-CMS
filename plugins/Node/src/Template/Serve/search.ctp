<?php
/**
 * Licensed under The GPL-3.0 License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since    2.0.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://www.quickappscms.org
 * @license  http://opensource.org/licenses/gpl-3.0.html GPL-3.0 License
 */
?>
<div class="row">
    <div class="col-md-12">
        <?php if (count($nodes)): ?>
            <h1><?php echo __d('node', 'Search Results'); ?></h1>

            <?php foreach ($nodes as $node): ?>
                <?php echo $this->render($node); ?>
            <?php endforeach; ?>

            <ul class="pagination">
                <?php echo $this->Paginator->options(['url' => ['_name' => 'node_search', 'criteria' => $criteria]]); ?>
                <?php echo $this->Paginator->prev(); ?>
                <?php echo $this->Paginator->numbers(); ?>
                <?php echo $this->Paginator->next(); ?>
            </ul>

            <p class="text-center help-block">
                <?php
                    echo $this->Paginator->counter(
                        __d('node', 'Page {{page}} of {{pages}}, showing {{current}} results out of {{count}} total.')
                    );
                ?>
            </p>
        <?php else: ?>
            <h2><?php echo __d('node', 'Your search yielded no results'); ?></h2>
            <ul>
                <li><?php echo __d('node', 'Check if your spelling is correct.'); ?></li>
                <li><?php echo __d('node', 'Remove quotes around phrases to search for each word individually, <code>white car</code> will often show more results than <code>"white car"</code>.'); ?></li>
                <li><?php echo __d('node', 'Consider loosening your query using <code>AND</code> & <code>OR</code>, <code>white OR car</code> will often show more results than <code>white cat</code>.'); ?></li>
            </ul>
        <?php endif; ?>
    </div>
</div>