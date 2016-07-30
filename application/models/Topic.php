<?php
namespace models;

use framework\Model;

class Topic extends Model
{
    public function policyId()
    {
        return 'default';
    }

    public function tableName()
    {
        return 'topic';
    }
    
    const STATUS_PUBLIC = 1;
    
    const STATUS_DRAFT = 2;
    
    const STATUS_DELETED = -1;
    
    const STATUS_PRIVATE = 3;
    
    private static $statusText = [
        self::STATUS_DELETED => '已删除',
        self::STATUS_DRAFT => '草稿',
        self::STATUS_PRIVATE => '私有',
        self::STATUS_PUBLIC => '公开',
    ];
    
    public function getStatusText($status = null)
    {
        if (is_null($status)) {
            return self::$statusText;
        }
        return isset(self::$statusText[$status]) ? self::$statusText[$status] : '未知';
    }
    
    
    public function getList($field = '*', array $where = array(), $orderby = '', $order = '', $limit = '20')
    {
        return $this->select(static::tableName(), $field, $where, $orderby, $order, empty($limit) ? 20 : $limit);
    }
    
}