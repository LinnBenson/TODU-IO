# 系统预设工具集

#### 便捷工具集 src/Helper/Tool.php
###### Todu\Helper\Tool
- 随机数生成器
  ```
  Tool::random( [number]:生成长度, [all|number|letter]|all:随机生成类型 );
  ```
  - return [string]:随机内容
- 配置文件覆盖写入
  ```
  Tool::coverConfig( [string]:文件路径, [array]:配置信息 );
  ```
  - return [boolean]:覆盖结果

#### Web 辅助工具 src/Helper/Web.php
###### Todu\Helper\Web
- 构造函数
  ```
  new Web( [string]:访问链接 );
  ```
  return Web:Class
- 创建文件上传对象
  ```
  Web::file( [string]:文件路径 );
  ```
  - return [\CURLFile]:文件对象
- 构建请求
  ```
  ->data( [array|json]请求数据 )
  ->header( [array]:请求头信息 )
  ->cookie( [string]:请求 Cookie )
  ->timeout( [number]:请求超时时间 )
  ->userAgent( [string]:用户代理 )
  ->referer( [string]:请求来源 )
  ->option( [array]:cURL 选项 )
  ->request( [string]|'GET':请求方法 );
  ```
  - return [Web]:Class
- 获取响应内容
  ```
  ->result; null|string // 原始内容
  ->status; null|number // HTTP 状态码
  ->info; null|array // 响应信息
  ->errno; null|number // 错误码
  ->error; null|string // 错误信息
  ->toArray(); null|array // 原始内容转数组
  ```
  - return 对应内容