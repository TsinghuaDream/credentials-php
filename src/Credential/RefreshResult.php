<?php

namespace AlibabaCloud\Credentials\Credential;

class RefreshResult
{
    public function __construct($credentials = [], $staleTime = null, $prefetchTime = null)
    {
        $this->credentials = $credentials;
        $this->staleTime   = $staleTime ? $staleTime : PHP_INT_MAX;
        $this->prefetchTime = $prefetchTime ? $prefetchTime : PHP_INT_MAX;
    }
    public function validate()
    {
    }
    public function toMap()
    {
        $res = [];
        if (null !== $this->staleTime) {
            $res['staleTime'] = $this->staleTime;
        }
        if (null !== $this->prefetchTime) {
            $res['prefetchTime'] = $this->prefetchTime;
        }
        if (null !== $this->credentials) {
            $res['credentials'] = $this->credentials;
        }
        return $res;
    }
    /**
     * @param array $map
     * @return RefreshResult
     */
    public static function fromMap($map = [])
    {
        $model = new self();
        if (isset($map['staleTime'])) {
            $model->staleTime = $map['staleTime'];
        }
        if (isset($map['prefetchTime'])) {
            $model->staleTime = $map['prefetchTime'];
        }
        if (isset($map['credentials'])) {
            $model->staleTime = $map['credentials'];
        }
        return $model;
    }
    /**
     * @description staleTime
     * @var int
     */
    public $staleTime;

    /**
     * @description prefetchTime
     * @var int
     */
    public $prefetchTime;

    /**
     * @description credentials
     * @var \AlibabaCloud\Credentials\Providers\Credentials
     */
    public $credentials;


    /**
     * @return \AlibabaCloud\Credentials\Providers\Credentials
     */
    public function credentials()
    {
        return $this->credentials;
    }

    /**
     * @var int
     */
    public function staleTime()
    {
        return $this->staleTime;
    }

    /**
     * @var int
     */
    public function prefetchTime()
    {
        return $this->prefetchTime;
    }

}