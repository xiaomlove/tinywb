<div class="container">
    <div class="jumbotron">
      <h1 class="display-3">Hello, world!</h1>
      <p class="lead">本站是DEMO演示而已，文字看不懂、数据不完整都是正常的。</p>
      <hr class="m-y-md">
      <form action="/search"><input name="keyword" size="8" placeholder="搜索" style="float: right"></form>
    </div>
    <div class="row">
        <div class="col-md-8">
        <?php if (!empty($list)):?>
        <?php foreach ($list as $article):?>
            <article class="article">
                <h2><?php echo $article['title']?></h2>
                <p>发表时间：<time class="date-time"><?php echo date('Y-m-d H:i:s', $article['publish_time'])?></time></p>
                <?php if (!empty($article['tagList'])):?>
                <p>标签：
                    <span class="tags">
                        <?php foreach ($article['tagList'] as $tag):?>
                        <a href="#"><?php echo $tag['name']?></a>
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
            <?php echo widgets\NewestArticle::widget()?>
            <div>
                <h2>热门文章</h2>
                <ul>
                    <li><a href="#">张三跳楼了</a></li>
                    <li><a href="#">张三跳楼了</a></li>
                </ul>
            </div>
            
            <?php echo widgets\SideDataStat::widget()?>
            
            <div>
                <h2>热门标签</h2>
                <ul class="list-inline">
                    <li class="list-inline-item"><a href="#">美国队长</a></li>
                    <li class="list-inline-item"><a href="#">PHP</a></li>
                    <li class="list-inline-item"><a href="#">张三丰</a></li>
                    <li class="list-inline-item"><a href="#">周剥光</a></li>
                    <li class="list-inline-item"><a href="#">阳顶天</a></li>
                    <li class="list-inline-item"><a href="#">五芒</a></li>
                    <li class="list-inline-item"><a href="#">开下第一</a></li>
                    <li class="list-inline-item"><a href="#">Music</a></li>
                    <li class="list-inline-item"><a href="#">美国</a></li>
                    <li class="list-inline-item"><a href="#">Mysql</a></li>
                    <li class="list-inline-item"><a href="#">苹果</a></li>
                    <li class="list-inline-item"><a href="#">iPhone</a></li>
                </ul>
            </div>
            
            
        </div>
    </div>
    
</div>