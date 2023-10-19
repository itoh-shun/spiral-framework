<?php
//https://www.agent-grow.com/self20percent/2018/05/09/phpunit_testing_private_and_protected_methods/

class MethodTester
{
    /** @var object テスト処理を実行するクラスのインスタンス */
    protected $___target_instance;

    /** @var object ReflectionClassのオブジェクト */
    protected $___reflect_obj;

    /**
     * MethodTester constructor.
     *
     * @param object $target_instance private, protectedメソッドを呼べるようにするインスタンス
     * @throws \ReflectionException
     */
    public function __construct($target_instance)
    {
        $this->___target_instance = $target_instance;
        $this->___reflect_obj     = new \ReflectionClass($target_instance);
    }

    public function __get($name)
    {
        $property = $this->___reflect_obj->getProperty($name);
        $property->setAccessible(true);
        return $property->getValue($this->___target_instance);
    }

    public function __set($name, $value)
    {
        $property = $this->___reflect_obj->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($this->___target_instance, $value);
    }

    public function __call($name, $arguments)
    {
        $method = $this->___reflect_obj->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($this->___target_instance, $arguments);
    }
}