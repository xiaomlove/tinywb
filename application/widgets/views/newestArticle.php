<?php if (!empty($newest)):?>
<div>
    <h2>最新文章</h2>
    <ul>
        <?php foreach ($newest as $item):?>
        <li><a href="#"><?php echo $item['title']?></a></li>
        <?php endforeach;?>
    </ul>
</div>
<?php endif?>