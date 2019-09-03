<?php

namespace AlibabaCloud\Credentials;

use ReflectionClass;
use ReflectionParameter;
use ReflectionException;
use InvalidArgumentException;

/**
 * Class Credential
 *
 * @package AlibabaCloud\Credentials
 *
 * @mixin AccessKeyCredential
 * @mixin BearerTokenCredential
 * @mixin EcsRamRoleCredential
 * @mixin RamRoleArnCredential
 * @mixin RsaKeyPairCredential
 */
class Credential
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $types = [
        'access_key'   => AccessKeyCredential::class,
        'sts'          => StsCredential::class,
        'ecs_ram_role' => EcsRamRoleCredential::class,
        'ram_role_arn' => RamRoleArnCredential::class,
        'rsa_key_pair' => RsaKeyPairCredential::class,
    ];

    /**
     * @var AccessKeyCredential|BearerTokenCredential|EcsRamRoleCredential|RamRoleArnCredential|RsaKeyPairCredential
     */
    protected $credential;

    /**
     * @var string
     */
    protected $type;

    /**
     * Credential constructor.
     *
     * @param array $config
     *
     * @throws ReflectionException
     */
    public function __construct(array $config = [])
    {
        if ($config !== []) {
            $this->config = array_change_key_case($config);
            $this->parseConfig();
        } else {
            $this->credential = Credentials::get()->getCredential();
        }
    }

    /**
     * @return AccessKeyCredential|BearerTokenCredential|EcsRamRoleCredential|RamRoleArnCredential|RsaKeyPairCredential
     */
    public function getCredential()
    {
        return $this->credential;
    }

    /**
     * @throws ReflectionException
     */
    private function parseConfig()
    {
        if (!isset($this->config['type'])) {
            throw  new InvalidArgumentException('Missing required type option');
        }

        $this->type = $this->config['type'];
        if (!isset($this->types[$this->type])) {
            throw  new InvalidArgumentException(
                'Invalid type option, support: ' .
                implode(', ', array_keys($this->types))
            );
        }

        $class      = new ReflectionClass($this->types[$this->type]);
        $parameters = [];
        /**
         * @var $parameter ReflectionParameter
         */
        foreach ($class->getConstructor()->getParameters() as $parameter) {
            $parameters[] = $this->getValue($parameter);
        }

        $this->credential = $class->newInstance(...$parameters);
    }

    /**
     * @param ReflectionParameter $parameter
     *
     * @return string
     * @throws ReflectionException
     */
    protected function getValue(ReflectionParameter $parameter)
    {
        if ($parameter->getType() && $parameter->getType()->getName() === 'array') {
            return $this->config;
        }

        foreach ($this->config as $key => $value) {
            if (strtolower($parameter->name) === $key) {
                return $value;
            }
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new InvalidArgumentException("Missing required {$parameter->name} option in config for {$this->type}");
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->credential->$name($arguments);
    }
}
