<div class="container">
    <div class="jumbotron">
      <h1 class="display-3"><a href="/">Hello, world!</a></h1>
      <p class="lead">本站是DEMO演示而已，文字看不懂、数据不完整都是正常的。</p>
      <hr class="m-y-md">
      <form action="/search"><input name="keyword" size="8" placeholder="搜索" style="float: right"></form>
    </div>
    <div class="row">
        <div class="col-md-8">
        <?php if (!empty($list)):?>
        <?php foreach ($list as $article):?>
            <article class="article">
                <h2 class="title"><a href="<?php echo url('controllers\Index@detail', ['id' => $article['id']])?>"><?php echo $article['title']?></a></h2>
                <p><small>发表时间：<time class="date-time"><?php echo date('Y-m-d H:i:s', $article['publish_time'])?></time></small></p>
                <?php if (!empty($article['tagList'])):?>
                <p>标签：
                    <span class="tags">
                        <?php foreach ($article['tagList'] as $tag):?>
                        <a href="<?php echo url('controllers\Index@tag', ['tagName' => $tag['name']])?>"><?php echo $tag['name']?></a>
                        <?php endforeach;?>
                    </span>
                </p>
                <?php endif?>
                
            </article>
           <?php endforeach;?>
           <?php else:?>
           <strong>oops!还没有文章。</strong>
           <?php endif?> 
           
           <nav><?php echo $pagination?></nav>
        </div>
        <div class="col-md-4">
            <?php
                echo widgets\NewestArticle::widget();
                echo widgets\SideHotArticle::widget();
                echo widgets\SideDataStat::widget();
                echo widgets\SideHotTag::widget();
            ?>
            
        </div>
    </div>
    
</div>