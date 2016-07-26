<div class="container">
    <div class="jumbotron">
      <h1 class="display-3">Hello, world!</h1>
      <p class="lead">本站是DEMO演示而已，文字看不懂、数据不完整都是正常的。</p>
      <hr class="m-y-md">
    </div>
    <div class="row">
        <div class="col-md-8">
        <?php if (!empty($list)):?>
        <?php foreach ($list as $article):?>
            <article class="article">
                <h2><?php echo $article['title']?></h2>
                <p>
                    <time class="date-time"><?php echo date('Y-m-d H:i:s', $article['publish_time'])?></time>
                    <span class="tags">
                        <a href="#">标签一</a>
                        <a href="#">标签二</a>
                    </span>
                </p>
            </article>
           <?php endforeach;?>
           <?php else:?>
           <strong>oops!还没有文章。</strong>
           <?php endif?> 
           
           <nav><?php echo $pagination?></nav>
           
            <!-- 
            <nav>
              <ul class="pagination">
                <li class="page-item">
                  <a class="page-link" href="#" aria-label="Previous">
                    <span aria-hidden="true">«</span>
                    <span class="sr-only">Previous</span>
                  </a>
                </li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                  <a class="page-link" href="#" aria-label="Next">
                    <span aria-hidden="true">»</span>
                    <span class="sr-only">Next</span>
                  </a>
                </li>
              </ul>
            </nav>
             -->
        </div>
        <div class="col-md-4">
            <div>
                <h2>最新文章</h2>
                <ul>
                    <?php if (!empty($newest)):?>
                    <?php foreach ($newest as $item):?>
                    <li><a href="#"><?php echo $item['title']?></a></li>
                    <?php endforeach;?>
                    <?php endif?>
                </ul>
            </div>
            
            <div>
                <h2>热门文章</h2>
                <ul>
                    <li><a href="#">张三跳楼了</a></li>
                    <li><a href="#">张三跳楼了</a></li>
                </ul>
            </div>
            
            <div>
                <h2>数据统计</h2>
                <ul>
                    <li>文章总数：<strong><?php echo $topicTotal?></strong></li>
                    <li>标签总数：<strong><?php echo $tagTotal?></strong></li>
                </ul>
            </div>
            
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