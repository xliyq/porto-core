<?php


namespace Porto\Core\Traits;


use Porto\Core\Dto\Dto;
use Porto\Core\Requests\Request;
use Porto\Core\Support\Facades\Porto;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ReflectionMethod;

trait CallableTrait
{
    /**
     * @param       $class
     * @param array $runMethodArguments
     * @param array $extraMethodsToCall
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function call($class, $runMethodArguments = [], $extraMethodsToCall = []) {
        $class = $this->resolveClass($class);

        $this->setUIIfExist($class);

        $this->callExtraMethods($class, $extraMethodsToCall);

        $runMethodArguments = $this->convertRequestsToDto($class, $runMethodArguments);

        return $class->run(...array_values($runMethodArguments));
    }

    /**
     * 以事务的方式调用
     *
     * @param       $class
     * @param array $runMethodArguments
     * @param array $extraMethodsToCall
     *
     * @return mixed
     * @throws \Throwable
     */
    public function transactionalCall($class, $runMethodArguments = [], $extraMethodsToCall = []) {
        return DB::transaction(function () use ($class, $runMethodArguments, $extraMethodsToCall) {
            return $this->call($class, $runMethodArguments, $extraMethodsToCall);
        });
    }

    private function setUIIfExist($class) {
        if (method_exists($class, 'setUI') && property_exists($this, 'ui')) {
            $class->setUI($this->ui);
        }
    }


    private function callExtraMethods($class, $extraMethodsToCall) {
        foreach ($extraMethodsToCall as $methodInfo) {
            if (is_array($methodInfo)) {
                $this->callWithArguments($class, $methodInfo);
            } else {
                $this->callWithoutArguments($class, $methodInfo);
            }
        }

    }

    private function callWithArguments($class, $methodInfo) {
        $method = key($methodInfo);
        $arguments = $methodInfo[$method];
        // 判断是否存在方法
        if (method_exists($class, $method)) {
            $class->$method(...$arguments);
        }
    }

    private function callWithoutArguments($class, $methodInfo) {
        if (method_exists($class, $methodInfo)) {
            $class->$methodInfo();
        }
    }

    /**
     * @param       $class
     * @param array $runMethodArguments
     *
     * @return array
     * @throws \ReflectionException
     */
    private function convertRequestsToDto($class, array $runMethodArguments = []) {
        $requestPositions = [];

        // 将产生中的Request 提取出来
        foreach ($runMethodArguments as $argumentPosition => $argument) {
            if ($argument instanceof Request) {
                $requestPositions[] = $argumentPosition;
            }
        }

        //检查参数是否包含Request 类型数据
        if (empty($requestPositions)) {
            return $runMethodArguments;
        }

        // 反射$class 类的run函数的参数
        $reflector = new ReflectionMethod($class, 'run');
        $calleeParameters = $reflector->getParameters();

        //将Request 参数 转换成对应的 Transporter
        foreach ($requestPositions as $requestPosition) {
            $parameter = $calleeParameters[$requestPosition];
            // 获取参数要求的类
            $parameterClass = $parameter->getClass();

            //判断是否为非有效类
            if (!(($parameterClass != null) && (class_exists($parameterClass->name)))) {
                continue;
            }

            $parameterClassName = $parameterClass->name;
            // 判断类名是否为 Transporter 的子类
            if (!is_subclass_of($parameterClassName, Dto::class)) {
                continue;
            }

            /** @var Request $request */
            $request = $runMethodArguments[$requestPosition];
            $dtoClass = $request->getDto();
            /** @var Dto $transporter */
            $dto = new $dtoClass;

            $dto->hydrate($request->all());

            $runMethodArguments[$requestPosition] = $dto;
        }
        return $runMethodArguments;
    }

    private function resolveClass($class) {
        if ($this->needsParsing($class)) {
            $parsedClass = $this->parseClassName($class);

            $containerName = $this->capitalizeFirstLetter($parsedClass[0]);
            $className = $parsedClass[1];

            Porto::verifyContainerExist($containerName);
            $class = $classFullName = Porto::buildClassFullName($containerName, $className);
        } else {
            if (Config::get('porto.logging.log-wrong-api-caller-style', true)) {
                Log::debug("调用方式错误，正确的调用方式为Boot::call(containerName@className) for $class");
            }
        }
        return App::make($class);
    }


    private function parseClassName($class, $delimiter = '@') {
        return explode($delimiter, $class);
    }

    private function needsParsing($class, $separator = '@') {
        return preg_match('/' . $separator . '/', $class);
    }

    /**
     * 将首字母转换为大写
     *
     * @param $string
     *
     * @return string
     */
    private function capitalizeFirstLetter($string) {
        return ucfirst($string);
    }
}