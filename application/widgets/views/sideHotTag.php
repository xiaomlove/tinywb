<?php if (!empty($list)):?>
<div>
    <h2>热门标签</h2>
    <ul class="list-inline">
        <?php foreach ($list as $tag):?>
        <li class="list-inline-item"><a href="<?php echo url('controllers\Index@tag', ['tagName' => $tag['name']])?>"><?php echo $tag['name']?></a></li>
        <?php endforeach;?>
    </ul>
</div>
<?php endif?>