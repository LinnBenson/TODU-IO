# 核心驱动器

#### 核心驱动器 src/Bootstrap.php
###### Todu\Bootstrap
- 驱动器初始化状态
  ```
  boolean Bootstrap::$init;
  ```
- 初始化驱动器
  ```
  Bootstrap::init( [callable|null]:初始化完成后的回调方法 );
  ```
  - return [mixed]:回调方法的返回值
- 线程缓存工具
  ```
  Bootstrap::cache( [string]:缓存名称, [callable]:回调方法 );
  ```
  - return [mixed]:返回缓存数据
- 插件权限介入
  ```
  Bootstrap::permission( [string]:权限名称, ['LAST','ALL', 'NULL']:回传方式, [mixed]|null:传递参数, ...[mixed]:附加参数 );
  ```
  - return [mixed]:传递参数

#### 系统通用函数 support/Helper/System.php
###### 需要 Bootstrap::init() 初始化后使用
- 导入文件
  ```
  import( [string]|[array]:文件路径 );
  ```
  - return [mixed]:返回引入内容
- 查询 ENV 环境变量
  ```
  // 这个函数在 init 完成后可直接使用 .env 配置文件
  env( [string]:环境变量键, [mixed]|null:默认值 );
  ```
  - return [mixed]:返回环境变量值
- 查询配置信息
  ```
  // 这个函数在 init 完成后可直接使用 config/***.php 配置文件
  config( [string]|[null]:配置信息键, [mixed]|null:默认值 );
  ```
  - return [mixed]:配置信息内容
- 读取语言包内容
  ```
  // 这个函数在 init 完成后可直接使用 resource/lang/*** 语言包文件
  __( [string]:键名, [array]|[]:替换内容, [string]|null:语言标识 );
  ```
  - return [string]:语言内容
- 获取插件实例
  ```
  plugin( [string]:插件名称 );
  ```
  - return [\Todu\Slot\Plugin]|null:返回插件实例