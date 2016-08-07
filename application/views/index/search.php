<div class="container">
    <div class="row" style="margin-top: 80px;margin-bottom: 20px">
        <div class="col-md-12">
            <h2 style="text-align: center">搜索测试---<a href="/">回首页</a></h2>
            <form>
                <div class="form-group">
                  <div class="input-group">
                      <input type="text" name="keyword" class="form-control" placeholder="输入关键字" value="<?php echo $keyword?>">
                      <span class="input-group-btn">
                        <button class="btn btn-success" type="submit">搜索</button>
                      </span>
                  </div>
                </div>
                <div class="form-group">
                    <div class="radio-inline"><label>排序字段</label></div>
                    <div class="radio-inline">
                      <label>
                        <input type="radio" name="orderby" value="1" <?php if ($orderby == 1):?>checked<?php endif?>>相关性
                      </label>
                    </div>
                    <div class="radio-inline">
                      <label>
                        <input type="radio" name="orderby" value="2" <?php if ($orderby == 2):?>checked<?php endif?>>发表时间
                      </label>
                    </div>
                    <div class="radio-inline">
                      <label>
                        <input type="radio" name="orderby" value="3" <?php if ($orderby == 3):?>checked<?php endif?>>更新时间
                      </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="radio-inline"><label>排序顺序</label></div>
                    <div class="radio-inline">
                      <label>
                        <input type="radio" name="order" value="1" <?php if ($order == 1):?>checked<?php endif?>>降序
                      </label>
                    </div>
                    <div class="radio-inline">
                      <label>
                        <input type="radio" name="order" value="2" <?php if ($order == 2):?>checked<?php endif?>>升序
                      </label>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12"> 
            <?php if (is_array($data)):?>
            <h5 style="margin-bottom: 20px"><strong class="text-danger"><?php echo sprintf("关键字：'%s'， 从 %d条记录中为你找到结果 %d条，花费时间 %f秒。", $keyword, $all, $total, $costTime)?></strong></h5>
            <ul class="list-unstyled search-result-ul">
                <?php foreach ($data as $key => $value):?>
                <li>
                    <h3><a href="<?php echo url('controllers\Index@detail', ['id' => $value['id']])?>"><?php echo $value['title']?></a></h3>
                    <p>
                        <span>发表于：<?php echo date('Y-m-d H:i:s', $value['publish_time'])?></span>
                        <span>更新于：<?php echo date('Y-m-d H:i:s', $value['update_time'])?></span>
                    </p>
                </li>
                <?php endforeach;?>
            </ul>
            <?php else:?>
            <strong class="text-danger"><?php echo $data?></strong>
            <?php endif?>
            <nav><?php echo $pagination?></nav>
        </div>
    </div>
</div>