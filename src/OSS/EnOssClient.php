<?php
// +----------------------------------------------------------------------
// | weather_new_server
// +----------------------------------------------------------------------
// | Date: 2017/3/18 14:37
// +----------------------------------------------------------------------
// | Author: liuzhengbin <liuzhengbin@ireadercity.com>
// +----------------------------------------------------------------------

namespace OSS;

use think\Config;
use think\Exception;

class EnOssClient extends OssClient {
    private $accesspoint;

    public function __construct($options = []) {
        $aliyunossconfig = Config::get("aliossconfig");
        $aliyunossconfig = array_merge($aliyunossconfig, $options);
        if (empty($aliyunossconfig['accessid']) || empty($aliyunossconfig['secretkey']) || empty($aliyunossconfig['endpoint']) || !is_bool($aliyunossconfig['iscaname']) || empty($aliyunossconfig['bucket'])) {
            throw new Exception("aliyun oss config error");
        }
        $this->accesspoint = $aliyunossconfig["accesspoint"];
        parent::__construct($aliyunossconfig['accessid'], $aliyunossconfig['secretkey'], $aliyunossconfig['endpoint'], $aliyunossconfig['iscaname'], NULL);
    }

    public function putObject($bucket, $object, $content, $options = NULL) {
        $data = parent::putObject($bucket, $object, $content, $options = NULL);
        //访问节点不为空的时候替换
        if (!empty($this->accesspoint)) {
            $urlinfo = parse_url($data['info']['url']);
            $data['info']['url'] = $urlinfo["scheme"] . "://" . $this->accesspoint . $urlinfo['path'];
            $data['oss-request-url'] = $urlinfo["scheme"] . "://" . $this->accesspoint . $urlinfo['path'];
        }
        return $data;
    }

    public function uploadFile($bucket, $object, $file, $options = NULL) {
        $data = parent::uploadFile($bucket, $object, $file, $options = NULL);
        //访问节点不为空的时候替换
        if (!empty($this->accesspoint)) {
            $urlinfo = parse_url($data['info']['url']);
            $data['info']['url'] = $urlinfo["scheme"] . "://" . $this->accesspoint . $urlinfo['path'];
            $data['oss-request-url'] = $urlinfo["scheme"] . "://" . $this->accesspoint . $urlinfo['path'];
        }
        return $data;
    }

}