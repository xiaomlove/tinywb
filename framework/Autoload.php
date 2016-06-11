<?php
/**
* @desc 自动加载类 
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月3日    上午2:41:08
*/

namespace framework;

use framework\traits\Singleton;

require __DIR__ . '/traits/Singleton.php';

class Autoload
{
    use Singleton;
    
    /**
     * 是否已经注册
     * @var unknown
     */
    private $isRegistered = false;
    
    /**
     * 前缀，key => value。key为命名空间前缀，value为该命名空间前缀下基目录组成的数组
     * @var unknown
     */
    private $prefixes = [];
    
    private $classMaps = [];//框架类映射，可以一定程度加快截入速度
    
    private $filePathMaps = [];//文件路径映射，通过完整类名获取其路径
    
    private $loadingClassName = '';//当前正在加载的完整类名    
    
    public function register()
    {
        if ($this->isRegistered) {
            return false;
        }
        $this->isRegistered = true;
        return spl_autoload_register([$this, 'loadClass'], true, true);
    }
    
    /**
     * 添加命名空间前缀和基目录
     * @param unknown $prefix
     * @param unknown $baseDir
     * @param string $prepend
     * @return boolean
     */
    public function addNamespace($prefix, $baseDir, $prepend = false)
    {
        $prefix = trim($prefix, '\\');//命名空间前缀左右都不带\我较认可
        $baseDir = rtrim($baseDir, '/\\');//目录右边都不带目录分割符。
        if (!isset($this->prefixes[$prefix])) {
            $this->prefixes[$prefix] = [];
        }
        if ($prepend) {
            array_unshift($this->prefixes[$prefix], $baseDir);
        } else {
            array_push($this->prefixes[$prefix], $baseDir);
        }
        return true;
    }
    
    /**
     * 添加类映射
     */
    public function addClassMap(array $classMap)
    {
        $this->classMaps = array_merge($this->classMaps, $classMap);
    }
    
    public function getNamespaces()
    {
        return $this->prefixes;
    }
    
    public function getClassMaps()
    {
        return $this->classMaps;
    }
    
    private function loadClass($className)
    {
        $this->loadingClassName = $className;
        if (isset($this->classMaps[$className])) {
            return $this->requireFile($this->classMaps[$className]);
        }
       // echo "try to load class: $className <br/>";
        $prefix = $className;
        while (($pos = strrpos($prefix, '\\')) !== false) {
            //依据PSR-4规范，每一个完整类名都有一个顶级命名空间，故\\肯定存在
            $prefix = substr($prefix, 0, $pos);//如framework，左右不带\
            $relativeClassName = substr($className, $pos + 1);//如App，左右不带\
            $loadMappedFile = $this->loadMappedFile($prefix, $relativeClassName);
            if ($loadMappedFile) {
                return true;
            }
            
        }
        return false;
    }
    
    /**
     * 从前缀映射数组截入文件
     * @param unknown $prefix
     * @param unknown $relativeClassName
     * @return boolean
     */
    private function loadMappedFile($prefix, $relativeClassName)
    {
        if (!isset($this->prefixes[$prefix])) {
            return false;
        }
        foreach ($this->prefixes[$prefix] as $baseDir) {
            $file = $baseDir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClassName) . '.php';
            if ($this->requireFile($file)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * 引入文件
     * @param unknown $file
     * @return boolean
     */
    private function requireFile($file)
    {
        if (file_exists($file)) {
            $this->filePathMaps[$this->loadingClassName] = $file;
            require $file;
            return true;
        }
        return false;
    }
    
    public function getFilePath($className = null)
    {
        if (is_null($className))
        {
            return $this->filePathMaps;
        } elseif (isset($this->filePathMaps[$className])) {
            return $this->filePathMaps[$className];
        } else {
            return '';
        }
        
    }
}