<div class="container">
    <div class="jumbotron">
      <h1 class="display-3"><a href="/">Hello, world!</a></h1>
      <p class="lead">本站是DEMO演示而已，文字看不懂、数据不完整都是正常的。</p>
      <hr class="m-y-md">
      <form action="/search"><input name="keyword" size="8" placeholder="搜索" style="float: right"></form>
    </div>
    <div class="row">
        <div class="col-md-8">
           <form>
              <div class="form-group">
                <label for="title">标题</label>
                <input type="text" class="form-control" id="title" placeholder="填写文章标题" value="<?php if (!empty($info['title'])) echo $info['title']?>">
              </div>
              <div class="form-group">
                <label for="detail">内容</label>
                <textarea id="detail" class="form-control" rows="10" placeholder="填写文章内容"><?php if (!empty($info['detail'])) echo $info['detail']?></textarea>
              </div>
              <div class="form-group">
                <p style="text-align: center"><input type="button" class="btn btn-primary" value="提交"></p>
              </div>
            </form>
        </div>
        <div class="col-md-4">
            <?php if (!empty($info['tags'])):?>
            <h4>已有标签：</h4>
            <ul class="list-inline">
                <?php foreach ($info['tags'] as $tag):?>
                <li class="list-inline-item tag"><?php echo $tag['name']?><i class="fa fa-trash-o" aria-hidden="true" title="删除"></i></li>
                <?php endforeach;?>
            </ul>
            <?php endif?>
            <h4>添加标签：</h4>
            <form class="form-inline">
              <div class="form-group">
                <input type="text" class="form-control" id="" placeholder="输入新标签，空格隔开">
              </div>
              <button type="button" class="btn btn-success">添加</button>
            </form>
        </div>
    </div>
    
</div>