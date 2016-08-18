<?php if (!empty($list)):?>
<div>
    <h2>热门文章</h2>
    <ul>
        <?php foreach ($list as $key => $value):?>
        <li><a href="<?php echo url('controllers\Index@detail', ['id' => $value['id']])?>"><?php echo $value['title']?></a></li>
        <?php endforeach;?>
    </ul>
</div>
<?php endif?>