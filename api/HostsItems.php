<?php
/**
 * Created by PhpStorm.
 * User: saive
 * Date: 17-1-3
 * Time: 下午10:10
 */
require_once '../system/bootstrap.php';

class HostsItems
{

    public static $data = array();
    public static $msg = '操作成功！';
    public static $code = 50100;

    /**
     * 获取全部的配置列表
     */
    public static function findAllHosts()
    {
        $hostPath = APP_PATH . '/hosts';
        $files = scandir($hostPath);
        foreach ($files as $key => $file) {
            if (substr($file, 0, 1) == '.') {
                unset($files[$key]);
            } else {
                $files[$key] = array(
                    'id' => $key,
                    'alias' => substr($file, 5),
                    'selected' => false,
                );
            }
        }

        array_unshift($files, array(
            'id' => 0,
            'alias' => 'system',
            'detail' => file_get_contents(DEFAULT_HOST_FILE),
            'selected' => true,
        ));

        return array(
            'code' => 2000,
            'data' => $files,
            'msg' => '查找成功！',
        );
    }

    /**
     * 检测配置名称是否可以使用
     * @param string $alias
     * @return bool
     */
    public static function checkAliasAvailabled($alias = '')
    {
        $alias = trim($alias);
        if ($alias == '') {
            return false;
        }

        return file_exists(APP_PATH . '/hosts/host-' . $alias);
    }

    /**
     * 创建配置
     * @param string $alias
     * @return bool
     */
    public static function buildHost($alias = '')
    {
        $alias = trim($alias);
        if ($alias == '') {
            return array(
                'code' => 50100,
                'data' => $alias,
                'msg' => '格式错误！',
            );
        }
        if (self::checkAliasAvailabled($alias)) {
            return array(
                'code' => 50101,
                'data' => null,
                'msg' => '配置文件已经存在！',
            );
        }
        if (touch(APP_PATH . '/hosts/host-' . $alias)) {
            return array(
                'code' => 2000,
                'data' => array(
                    'alias' => $alias,
                    'selected' => false,
                ),
                'msg' => '配置文件创建成功！',
            );
        } else {
            return array(
                'code' => 50102,
                'data' => null,
                'msg' => '创建失败！',
            );
        }
    }

    /**
     * 删除host！
     * @param $alias
     * @return array
     */
    public static function deleteHost($alias)
    {
        $hostFile = APP_PATH . '/hosts/host-' . $alias;
        if (file_exists($hostFile)) {
            unlink($hostFile);
        }

        return array(
            'code' => 2000,
            'data' => null,
            'msg' => '删除成功！',
        );
    }

    /**
     * 保存配置文件
     * @param $content
     * @param $alias
     * @return array
     */
    public static function saveHost($content, $alias)
    {
        if (!self::checkAliasAvailabled($alias)) {
            return array(
                'code' => 50100,
                'data' => null,
                'msg' => 'Host文件未找到~',
            );
        }
        $host = self::formatItem($content);
        file_put_contents(APP_PATH . '/hosts/host-' . $alias, $host);

        return array(
            'code' => 2000,
            'data' => null,
            'msg' => '更新成功~',
        );
    }

    /**
     * 应用配置
     * @param $alias
     * @return array
     */
    public static function useHost($alias)
    {
        $hostAlias = file_get_contents(APP_PATH . '/hosts/host-' . $alias);
        $common = file_get_contents(APP_PATH . '/hosts/host-common');
        $host = $common . "\n" . $hostAlias;
        file_put_contents(DEFAULT_HOST_FILE, $host);
        return array(
            'code' => '2000',
            'data' => null,
            'msg' => '应用成功！',
        );
    }

    /**
     * 先到先得原则实现host绑定
     * @param $host
     * @return string
     */
    public static function formatItem($host)
    {
        $hosts = explode("\n", $host);
        foreach ($hosts as $item) {
            $items = explode(' ', $item);
            $domain = end($items);
            if ($domain && !isset($res[$domain])) {
                $res[$domain] = trim($items[0]) . '    ' . $domain;
            }
        }

        return implode("\n", $res);
    }

    /**
     * 查找host文件内容
     * @param string $itemName
     * @return array
     */
    public static function findHost($itemName = '')
    {
        $itemName = trim($itemName);
        if ($itemName == 'system') {
            return array(
                'code' => 2000,
                'data' => file_get_contents(DEFAULT_HOST_FILE),
                'msg' => 'Host文件内容查找成功!',
            );
        }

        $hostFile = APP_PATH . '/hosts/host-' . $itemName;
        $hosts = file_get_contents($hostFile);
        if ($itemName != 'common') {
            $commonHosts = file_get_contents(APP_PATH . '/hosts/host-common');
            $hosts = self::removeCommonHosts($commonHosts, $hosts);
        }

        return array(
            'code' => 2000,
            'data' => $hosts,
            'msg' => 'Host文件内容查找成功!',
        );
    }

    /**
     * 移除公共部分的设置
     * @param $commonHost
     * @param $hosts
     * @return string
     */
    public static function removeCommonHosts($commonHost, $hosts)
    {
        $commonHost = self::_host2Array($commonHost);
        $hosts = self::_host2Array($hosts);
        foreach ($hosts as $key => $host) {
            if (!empty($commonHost[$key])) {
                unset($hosts[$key]);
            }
        }

        return implode("\n", $hosts);
    }

    /**
     * Host文件转数组    域名=>IP   域名     ['demo.com'] => '127.0.0.1 demo.com',
     * @param $hosts
     * @return array
     */
    public function _host2Array($hosts)
    {
        $hosts = explode("\n", $hosts);
        $res = array();
        foreach ($hosts as $host) {
            $host = trim($host);
            $items = explode(" ", $host);
            $domain = end($items);
            $res[$domain] = $items[0] . '    ' . $domain;
        }

        return $res;
    }

}

$cate = isset($_GET['cate']) ? $_GET['cate'] : 'find';

switch ($cate) {
    case 'find':
        $res = HostsItems::findAllHosts();
        break;
    case 'detail':
        $res = HostsItems::findHost($_GET['alias']);
        break;
    case 'save':
        $res = HostsItems::saveHost($_POST['content'], $_POST['alias']);
        break;
    case 'build':
        $res = HostsItems::buildHost($_POST['alias']);
        break;
    case 'del':
        $res = HostsItems::deleteHost($_POST['alias']);
        break;
    case 'use':
        $res = HostsItems::useHost($_POST['alias']);
        break;
}


echo json_encode($res);
exit;








