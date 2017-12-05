<?php
/**
 * UserCenter Response
 */
namespace ReMind\Api\ReMind\Base;

// 支持多种格式返回，自定义实现类并加载

class Response
{

    /**
     * 输出内容
     * @var
     */
    public $output;

    /**
     * Response constructor.
     * @param string $responseType
     * @param string $encode
     */
    public function __construct($httpCode = 200)
    {

    }

    /**
     * 返回json格式数据
     * @param array $outArr
     * @return mixed
     * @throws \Exception
     */
    public function Json($outArr= [])
    {
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($outArr);
        exit();
    }

    /**
     * 返回文本格式数据
     * @param string $outArr
     * @return mixed
     * @throws \Exception
     */
    public function Text($outArr= '')
    {

        $className          = str_replace(__NAMESPACE__, '', __CLASS__);
        $controllerName     = __NAMESPACE__.$className.'Text';

        die(new $controllerName($outArr));

    }


    public function Redirect($url = '')
    {
        $className          = str_replace(__NAMESPACE__, '', __CLASS__);
        $controllerName     = __NAMESPACE__.$className.'Redirect';

        new $controllerName($url);
    }

    public function __set($name, $value)
    {
        // TODO: Implement __set() method.
        $this->$name    = $value;
    }

    public function __get($name)
    {
        // TODO: Implement __get() method.
        return isset($this->$name) ? $this->$name : '';
    }
    public function success($outArr = [])
    {
        $this->Json(
            [
                'code'  => 0,
                'msg'   => 'success',
                'data'  => $outArr,
            ]
    );

    }
}