<?php
declare(strict_types=1);

namespace Liwanyi\Utils2;

class Image
{
    /**
     * 下载远程图片到本地
     * @param $url
     * @param $fileName
     * @param $dirName
     * @param $fileType
     * @param $type
     * @return array|false
     */
   public static function download_image($url, $fileName = null, $dirName = null, $fileType = array('jpg', 'gif', 'png'), $type = 1)
    {
        if ($url == '') {
            return false;
        }

        // 获取文件原文件名
        $defaultFileName = basename($url);

        // 获取文件类型
        $suffix = substr(strrchr($url, '.'), 1);
        if (!in_array($suffix, $fileType)) {
            return false;
        }

        // 设置保存后的文件名
        $fileName = $fileName == '' ? time() . rand(0, 9) . '.' . $suffix : $defaultFileName;

        // 获取远程文件资源
        if ($type) {
            $ch = curl_init();
            $timeout = 30;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $file = ob_get_contents();
            ob_end_clean();
        }

        // 设置文件保存路径
        //$dirName = $dirName . '/' . date('Y', time()) . '/' . date('m', time()) . '/' . date('d', time());
        $dirName = $dirName . '/base64';
        if (!file_exists($dirName)) {
            mkdir($dirName, 0777, true);
        }

        // 保存文件
        $res = fopen($dirName . '/' . $fileName, 'a');
        fwrite($res, $file);
        fclose($res);

        return array(
            'fileName' => $fileName,
            'saveDir' => $dirName
        );
    }


    /**
     * 压缩包安装
     * @param array $image_data
     * @return void
     */
    function imageDownAll(array $image_data)
    {
        $dfile = tempnam('/tmp', 'tmp');//产生一个临时文件，用于缓存下载文件
        $zip = new \ZipArchive();
        $filename = date('Y-m-d h:i:s').'-'.'image.zip'; //下载的默认文件名$filename = iconv("UTF-8", "GBK", $filename);//以下是需要下载的图片数组信息，将需要下载的图片信息转化为类似即可
        $image = $image_data;
        // 不进行证书验证
        $stream_opts = [
            "ssl" => [
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ]
        ];
        foreach ($image as $v) {
            $zip->addFile(file_get_contents($v['image_src'],false,stream_context_create($stream_opts)), $v['image_name']);
            // 添加打包的图片，第一个参数是图片内容，第二个参数是压缩包里面的显示的名称, 可包含路径
            // 或是想打包整个目录 用 $zip->add_path($image_path);
        }
        // 下载文件
        //----------------------
        $zip->output($dfile);
        ob_clean();
        header('Pragma: public');
        header('Last-Modified:' . gmdate('D, d M Y H:i:s') . 'GMT');
        header('Cache-Control:no-store, no-cache, must-revalidate');
        header('Cache-Control:pre-check=0, post-check=0, max-age=0');
        header('Content-Transfer-Encoding:binary');
        header('Content-Encoding:none');
        header('Content-type:multipart/form-data');
        header('Content-Disposition:attachment; filename="' . $filename . '"'); //设置下载的默认文件名
        header('Content-length:' . filesize($dfile));
        $fp = fopen($dfile, 'r');
        while (connection_status() == 0 && $buf = @fread($fp, 8192)) {
            echo $buf;
        }
        fclose($fp);
        @unlink($dfile);
        @flush();
        @ob_flush();
    }

}